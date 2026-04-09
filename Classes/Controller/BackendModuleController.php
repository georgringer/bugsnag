<?php

declare(strict_types=1);

namespace GeorgRinger\Bugsnag\Controller;

use GeorgRinger\Bugsnag\Exception;
use GeorgRinger\Bugsnag\Service\BugsnagService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;

#[AsController]
readonly class BackendModuleController
{
    public function __construct(
        private ModuleTemplateFactory $moduleTemplateFactory,
        private BugsnagService $bugsnagService,
        private FlashMessageService $flashMessageService,
        private SiteFinder $siteFinder,
        private PageRenderer $pageRenderer,
    ) {}

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $action = $request->getParsedBody()['action'] ?? '';
            if ($action === 'testException') {
                $this->runTestException();
            }
        }

        return $this->renderIndex($request);
    }

    private function renderIndex(ServerRequestInterface $request): ResponseInterface
    {
        $site = $this->getSiteFromRequest($request);

        $performanceSettings = null;
        if ($site !== null) {
            $this->pageRenderer->loadJavaScriptModule('@GeorgRinger/bugsnag/test-performance.js');
            $performanceSettings = [
                'apiKeyConfigured' => $site->getSettings()->get('bugsnag.apiKey', '') !== '',
                'apiKey' => $site->getSettings()->get('bugsnag.apiKey', ''),
                'jsPath' => $site->getSettings()->get('bugsnag.path', '//d2wy8f7a9ursnm.cloudfront.net/v1/bugsnag-performance.min.js'),
            ];
        }

        $view = $this->moduleTemplateFactory->create($request);
        $view->assignMultiple([
            'actionUrl' => (string)$request->getUri(),
            'apiKeyConfigured' => $this->bugsnagService->getApiKey($site) !== '',
            'apiKeySource' => $this->bugsnagService->getApiKeySource($site),
            'performanceSettings' => $performanceSettings,
        ]);

        return $view->renderResponse('BackendModule/Index');
    }

    private function getSiteFromRequest(ServerRequestInterface $request): ?Site
    {
        $pageId = (int)($request->getQueryParams()['id'] ?? $request->getParsedBody()['id'] ?? 0);
        if ($pageId === 0) {
            return null;
        }
        try {
            return $this->siteFinder->getSiteByPageId($pageId);
        } catch (\Throwable) {
            return null;
        }
    }

    private function runTestException(): void
    {
        try {
            $this->bugsnagService->sendException(new Exception('Test Exception from BE module'));
            $this->addFlashMessage('Test exception was sent to Bugsnag.', 'Success', ContextualFeedbackSeverity::OK);
        } catch (\Throwable $e) {
            $this->addFlashMessage($e->getMessage(), 'Failed to send test exception', ContextualFeedbackSeverity::ERROR);
        }
    }

    private function addFlashMessage(string $message, string $title, ContextualFeedbackSeverity $severity): void
    {
        $this->flashMessageService
            ->getMessageQueueByIdentifier()
            ->enqueue(new FlashMessage($message, $title, $severity, true));
    }
}
