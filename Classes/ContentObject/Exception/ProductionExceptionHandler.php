<?php
declare(strict_types=1);
namespace MichielRoos\Bugsnag\ContentObject\Exception;

use MichielRoos\Bugsnag\Service\BugsnagService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

/**
 * Class ProductionExceptionHandler
 *
 * Sends Exception to Bugsnag
 *
 * @package MichielRoos\Bugsnag\Core\Error
 */
class ProductionExceptionHandler extends \TYPO3\CMS\Frontend\ContentObject\Exception\ProductionExceptionHandler
{
    /**
     * Displays the given exception AND sends it to Bugsnag
     *
     * Handles exceptions thrown during rendering of content objects
     * The handler can decide whether to re-throw the exception or
     * return a nice error message for production context.
     *
     * @param \Exception $exception
     * @param AbstractContentObject $contentObject
     * @param array $contentObjectConfiguration
     * @return string
     * @throws \Exception
     */
    public function handle(\Exception $exception, ?AbstractContentObject $contentObject = null, $contentObjectConfiguration = []): string
    {
        $bugsnagService = GeneralUtility::makeInstance(BugsnagService::class);
        $bugsnagService->sendException($exception);
        return parent::handle($exception, $contentObject, $contentObjectConfiguration);
    }
}
