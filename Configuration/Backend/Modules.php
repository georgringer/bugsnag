<?php

declare(strict_types=1);

return [
    'tools_bugsnag' => [
        'parent' => 'content_status',
        'access' => 'admin',
        'path' => '/module/web/info/bugsnag',
        'iconIdentifier' => 'bugsnag-module',
        'labels' => 'LLL:EXT:bugsnag/Resources/Private/Language/locallang_mod.xlf',
        'routes' => [
            '_default' => [
                'target' => \GeorgRinger\Bugsnag\Controller\BackendModuleController::class . '::handleRequest',
            ],
        ],
    ],
];
