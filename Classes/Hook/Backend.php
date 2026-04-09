<?php

declare(strict_types=1);

namespace MichielRoos\Bugsnag\Hook;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;

class Backend
{
    public function addRequireJsConfiguration(array $params, PageRenderer $pageRenderer): void
    {
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()
        ) {
            $pageRenderer->loadJavaScriptModule('@michielroos/bugsnag/test-exception.js');
        }
    }
}
