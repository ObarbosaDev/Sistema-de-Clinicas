<?php

declare(strict_types=1);

use Clinica\Core\Database;
use Clinica\Core\Env;
use Clinica\Core\ErrorHandler;
use Clinica\Core\Session;

define('BASE_PATH', dirname(__DIR__));

$composerAutoload = BASE_PATH . '/vendor/autoload.php';

if (is_file($composerAutoload)) {
    require_once $composerAutoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'Clinica\\';

        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = __DIR__ . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass)
            . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    });
}

require_once __DIR__ . '/Core/helpers.php';

Env::load(BASE_PATH . '/.env');

/** @var array<string, mixed> $config */
$config = require __DIR__ . '/Config/app.php';

define('APP_NAME', (string) $config['app']['name']);

date_default_timezone_set($config['app']['timezone']);
ErrorHandler::register($config['app']);
Session::start($config['session']);
Database::configure($config['database']);

return $config;
