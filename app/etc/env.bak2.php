<?php
return [
    'backend' => [
        'frontName' => 'admin_1vfaw4'
    ],
    'crypt' => [
        'key' => 'ab3b6db5745ac47c63879f410022bb8d'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'broadout2.3.2',
                'username' => 'root',
                'password' => 'e8ZyjfpPXk77kGpR',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'production',
    'session' => [
        'save' => 'redis',
        'redis' => [
            'host' => '127.0.0.1',
            'port' => '6379',
            'password' => '',
            'timeout' => '2.5',
            'persistent_identifier' => '',
            'database' => '0',
            'compression_threshold' => '2048',
            'compression_library' => 'gzip',
            'log_level' => '1',
            'max_concurrency' => '12',
            'break_after_frontend' => '5',
            'break_after_adminhtml' => '30',
            'first_lifetime' => '600',
            'bot_first_lifetime' => '60',
            'bot_lifetime' => '7200',
            'disable_locking' => '0',
            'min_lifetime' => '60',
            'max_lifetime' => '2592000'
        ]
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => [
                    'server' => '127.0.0.1',
                    'port' => '6379',
                    'persistent' => '',
                    'database' => '0',
                    'password' => '',
                    'force_standalone' => '0',
                    'connect_retries' => '1',
                    'read_timeout' => '10',
                    'automatic_cleaning_factor' => '0',
                    'compress_data' => '1',
                    'compress_tags' => '1',
                    'compress_threshold' => '20480',
                    'compression_lib' => 'gzip',
                    'use_lua' => '0'
                ]
            ],
            'page_cache' => [
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => [
                    'server' => '127.0.0.1',
                    'port' => '6379',
                    'persistent' => '',
                    'database' => '1',
                    'password' => '',
                    'force_standalone' => '0',
                    'connect_retries' => '1',
                    'lifetimelimit' => '57600',
                    'compress_data' => '0'
                ]
            ]
        ]
    ],
    'lock' => [
        'provider' => 'db',
        'config' => [
            'prefix' => null
        ]
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
        'vertex' => 1
    ],
    'install' => [
        'date' => 'Tue, 10 Dec 2019 05:45:04 +0000'
    ],
    'downloadable_domains' => [
        'www.broadout.com',
        'www.broadout.jp'
    ]
];
