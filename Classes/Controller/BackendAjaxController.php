<?php

declare(strict_types=1);

namespace MichielRoos\Bugsnag\Controller;

use MichielRoos\Bugsnag\Exception;
use MichielRoos\Bugsnag\Service\BugsnagService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendAjaxController
{
    /**
     * Fire a test exception, so we can check if we correctly configured the API key
     * @throws \Exception
     */
    public function testAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        $exception = new Exception('Test Exception');
        $bugsnagService = GeneralUtility::makeInstance(BugsnagService::class);
        $bugsnagService->sendException($exception);

        $message = GeneralUtility::makeInstance(
            FlashMessage::class,
            'Test Exception sent to Bugsnag',
            'Exception sent',
            \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::INFO,
            true
        );
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $defaultFlashMessageQueue->enqueue($message);

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $url = $uriBuilder->buildUriFromRoute('main');
        return new RedirectResponse($url);
    }
}
