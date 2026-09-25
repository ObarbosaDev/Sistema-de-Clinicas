<?php

declare(strict_types=1);

use Clinica\Core\Auth;
use Clinica\Core\Router;

require dirname(__DIR__) . '/app/bootstrap.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; object-src 'none'; frame-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
header('Cache-Control: no-store, private');

$route = $_GET['route'] ?? (Auth::check() ? 'dashboard' : 'login');

if (!is_string($route)) {
    $route = 'dashboard';
}

/** @var Router $router */
$router = require dirname(__DIR__) . '/app/routes.php';
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $route);
