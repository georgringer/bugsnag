<?php

declare(strict_types=1);

namespace GeorgRinger\Bugsnag\Core\Error;

use GeorgRinger\Bugsnag\Service\BugsnagService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Sends Exception to Bugsnag
 */
class ProductionExceptionHandler extends \TYPO3\CMS\Core\Error\ProductionExceptionHandler
{
    /**
     * Displays the given exception AND sends it to Bugsnag
     *
     * @param \Throwable $exception The throwable object.
     *
     * @throws \Exception
     */
    public function handleException(\Throwable $exception): void
    {
        $bugsnagService = GeneralUtility::makeInstance(BugsnagService::class);
        $bugsnagService->sendException($exception);
        parent::handleException($exception);
    }
}
