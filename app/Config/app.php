<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => env('APP_NAME', 'Sistema de Controle Clínico'),
        'environment' => env('APP_ENV', 'production'),
        'debug' => env_bool('APP_DEBUG', false),
        'timezone' => env('APP_TIMEZONE', 'America/Sao_Paulo'),
    ],
    'database' => [
        'host' => env('DB_HOST', 'localhost'),
        'port' => (int) env('DB_PORT', '3306'),
        'name' => env('DB_DATABASE', 'clinica'),
        'username' => env('DB_USERNAME', 'clinica_app'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'name' => env('SESSION_NAME', 'clinica_session'),
        'secure' => env_bool('SESSION_SECURE', false),
        'same_site' => env('SESSION_SAME_SITE', 'Lax'),
    ],
];
