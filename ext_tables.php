<?php

declare(strict_types=1);

use MichielRoos\Bugsnag\Hook\Backend;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// todo: replace with better implementation
//$GLOBALS['TYPO3_USER_SETTINGS']['columns']['bugsnagTestException'] = [
//    'type'     => 'user',
//    'label'    => 'LLL:EXT:bugsnag/Resources/Private/Language/locallang.xlf:bugsnagTestException',
//    'userFunc' => function () {
//        return '<br><button type="button" id="testBugsnagException" class="btn btn-default">Generate test exception</button>';
//    }
//];
//
//ExtensionManagementUtility::addFieldsToUserSettings(
//    'LLL:EXT:bugsnag/Resources/Private/Language/locallang.xlf:bugsnagTestException,bugsnagTestException',
//    'after:resetConfiguration'
//);
//
//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] =
//    \MichielRoos\Bugsnag\Hook\Backend::class . '->addRequireJsConfiguration';
