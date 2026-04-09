<?php

namespace GeorgRinger\Bugsnag\Service;

use Bugsnag\Client;
use Bugsnag\Handler;
use Bugsnag\Report;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Sends Exception to Bugsnag
 */
class BugsnagService
{
    public function getApiKey(?Site $site = null): string
    {
        if ($site !== null) {
            $siteApiKey = $site->getSettings()->get('bugsnag.apiKey', '');
            if ($siteApiKey !== '') {
                return $siteApiKey;
            }
        }
        $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['bugsnag'] ?? [];
        return ($extensionConfiguration['apiKey'] ?? '') ?: (getenv('BUGSNAG_API_KEY') ?: '');
    }

    public function getApiKeySource(?Site $site = null): string
    {
        if ($site !== null && $site->getSettings()->get('bugsnag.apiKey', '') !== '') {
            return 'site setting bugsnag.apiKey';
        }
        $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['bugsnag'] ?? [];
        if (!empty($extensionConfiguration['apiKey'])) {
            return 'extension configuration';
        }
        if (getenv('BUGSNAG_API_KEY')) {
            return 'environment variable BUGSNAG_API_KEY';
        }
        return 'not configured';
    }

    /**
     * Sends exception to Bugsnag
     */
    public function sendException(\Throwable $exception): void
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $site = $request?->getAttribute('site');
        $bugsnagApiKey = $this->getApiKey($site instanceof Site ? $site : null);

        if ($bugsnagApiKey !== '') {
            $bugsnag = Client::make($bugsnagApiKey);

            if (PHP_SAPI === 'cli') {
                $appType = 'CLI';
            } elseif ($request !== null && ApplicationType::fromRequest($request)->isBackend()) {
                $appType = 'BE';
            } else {
                $appType = 'FE';
            }
            $bugsnag->setAppType($appType);
            $bugsnag->setReleaseStage((string)Environment::getContext());

            Handler::register($bugsnag);
            $bugsnag->notifyException($exception, function (Report $report) use ($appType, $request): void {
                $metadata = [
                    'typo3' => [
                        'version' => VersionNumberUtility::getNumericTypo3Version(),
                        'context' => (string)Environment::getContext(),
                    ],
                    'request' => [
                        'url' => $_SERVER['REQUEST_URI'] ?? null,
                        'method' => $_SERVER['REQUEST_METHOD'] ?? null,
                        'referer' => $_SERVER['HTTP_REFERER'] ?? null,
                    ],
                    'server' => [
                        'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                        'php_version' => PHP_VERSION,
                        'composer_mode' => Environment::isComposerMode(),
                    ],
                ];

                if ($request !== null && $appType === 'BE') {
                    $beUser = $GLOBALS['BE_USER'] ?? null;
                    if ($beUser !== null && isset($beUser->user['uid'])) {
                        $metadata['typo3']['be_user'] = [
                            'uid' => $beUser->user['uid'],
                            'username' => $beUser->user['username'],
                        ];
                    }
                } elseif ($request !== null && $appType === 'FE') {
                    $site = $request->getAttribute('site');
                    $routing = $request->getAttribute('routing');
                    if ($site instanceof Site) {
                        $metadata['typo3']['site'] = $site->getIdentifier();
                    }
                    if ($routing instanceof PageArguments) {
                        $metadata['typo3']['PageId'] = $routing->getPageType() === '0' ? $routing->getPageId() : (sprintf('%s [%s]', $routing->getPageId(), $routing->getPageType()));

                        $routingInfo = array_filter([
                            'arguments' => $routing->getArguments(),
                            'staticArguments' => $routing->getStaticArguments(),
                            'dynamicArguments' => $routing->getDynamicArguments(),
                            'queryArguments' => $routing->getQueryArguments(),
                            'routeArguments' => $routing->getRouteArguments(),
                        ]);
                        if (!empty($routingInfo)) {
                            $metadata['routing'] = $routingInfo;
                        }
                    }
                    $language = $request->getAttribute('language');
                    if ($language instanceof SiteLanguage) {
                        $metadata['typo3']['language'] = sprintf('%s (%s)', (string)$language->getLocale(), $language->getLanguageId());
                    }
                }

                $report->setMetaData($metadata);
            });
            $bugsnag->flush();
        }
    }
}
