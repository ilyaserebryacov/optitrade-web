<?php

return [
    // Конфигурация для локальной разработки (development)
    'development' => [
        'app' => [
            'name' => 'OptiTrade (Dev)',
            'debug' => true,
        ],
        'db' => [
            'driver' => 'pgsql',
            'host' => getenv('DB_HOST') ?: '127.0.0.1',
            'port' => getenv('DB_PORT') ?: 5432,
            'database' => getenv('DB_DATABASE') ?: 'optitrade',
            'username' => getenv('DB_USERNAME') ?: 'postgres',
            'password' => getenv('DB_PASSWORD') ?: 'secret',
            'charset' => 'utf8',
            'prefix' => '',
        ],
        'logging' => [
            'channel' => 'optitrade_dev',
            'file' => dirname(__DIR__) . '/storage/logs/app.log',
            'level' => 'DEBUG',
        ],
    ],

    // Конфигурация для боевого сервера (production) с поддержкой шифрования
    'production' => [
        'app' => [
            'name' => 'OptiTrade ERP',
            'debug' => false,
        ],
        'db' => [
            'driver' => 'pgsql',
            'host' => getenv('DB_HOST') ?: 'db-prod.internal',
            'port' => 5432,
            'database' => 'optitrade_production',
            'username' => 'opti_admin',
            // Пример зашифрованного пароля (начинается с encrypted:)
            'password' => 'encrypted:ZVhZc2RmL2FzZGYrMTIzNGFzZGY=',
            'charset' => 'utf8',
            'prefix' => '',
        ],
        'logging' => [
            'channel' => 'optitrade_prod',
            'file' => dirname(__DIR__) . '/storage/logs/production.log',
            'level' => 'INFO',
        ],
    ],
];
