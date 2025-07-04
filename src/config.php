<?php

return [
    'components' => [
        'staticUrlRule' => [
            'class' => 'bug32\\staticUrl\\components\\StaticUrlRule',
            'cacheEnabled' => true,
             'cacheDuration' => 3600,
             'autoClearCache' => true,
        ],
    ],
    'modules' => [
        'static-url' => [
            'class' => 'bug32\\staticUrl\\StaticUrlExtension',
             'adminRoute' => 'static-url/backend',
             'enableConsoleCommands' => true,
             'enableAdminInterface' => true,
             'defaultStatus' => 10,
             'urlValidationPattern' => '/^[a-z0-9\-_\/]+$/',
        ],
    ],
]; 