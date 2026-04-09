<?php

$EM_CONF['bugsnag'] = [
    'title' => 'Bugsnag exception handler',
    'description' => 'Bugsnag exception handler, sends exceptions to bugsnag',
    'category' => 'fe',
    'author' => 'Michiel Roos',
    'author_company' => 'Michiel Roos',
    'author_email' => 'michiel@michielroos.com',
    'state' => 'stable',
    'version' => 'v12.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.4.99',
        ],
    ],
    'autoload' => [
        'psr-4' => ['MichielRoos\\Busnag\\' => 'Classes'],
    ],
];
