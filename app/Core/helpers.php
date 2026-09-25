<?php

declare(strict_types=1);

use Clinica\Core\Auth;
use Clinica\Core\Csrf;
use Clinica\Core\Session;

function env(string $key, mixed $default = null): mixed
{
    $value = getenv($key);

    return $value === false ? $default : $value;
}

function app_name(): string
{
    return defined('APP_NAME') ? (string) APP_NAME : 'Sistema de Controle Clínico';
}

function env_bool(string $key, bool $default = false): bool
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOL);
}

function e(mixed $value): string
{
    if (!is_scalar($value) && $value !== null) {
        return '';
    }

    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** @param array<string, scalar|null> $parameters */
function url(string $route, array $parameters = []): string
{
    $query = array_merge(['route' => trim($route, '/')], $parameters);

    return 'index.php?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
}

function asset(string $path): string
{
    return 'assets/' . ltrim($path, '/');
}

/** @param array<string, scalar|null> $parameters */
function redirect(string $route, array $parameters = [], int $status = 303): never
{
    header('Location: ' . url($route, $parameters), true, $status);
    exit;
}

function csrf_field(): string
{
    return Csrf::field();
}

/** @return array<string, mixed> */
function old_input(): array
{
    $old = Session::flashed('old', []);

    return is_array($old) ? $old : [];
}

function old(string $key, mixed $default = ''): mixed
{
    $old = old_input();

    return $old[$key] ?? $default;
}

/** @return array<string, list<string>> */
function validation_errors(): array
{
    $errors = Session::flashed('errors', []);

    return is_array($errors) ? $errors : [];
}

function field_error(string $field): ?string
{
    $errors = validation_errors();
    $message = $errors[$field][0] ?? null;

    return is_string($message) ? $message : null;
}

function invalid_class(string $field): string
{
    return field_error($field) !== null ? ' is-invalid' : '';
}

function flash_message(string $key): ?string
{
    $message = Session::flashed($key);

    return is_string($message) ? $message : null;
}

function route_is(string ...$routes): bool
{
    $current = is_string($_GET['route'] ?? null) ? trim($_GET['route'], '/') : 'dashboard';

    return in_array($current, $routes, true);
}

function format_date(?string $date): string
{
    if ($date === null || $date === '') {
        return '—';
    }

    $timestamp = strtotime($date);

    return $timestamp === false ? '—' : date('d/m/Y', $timestamp);
}

function format_time(?string $time): string
{
    return $time === null || $time === '' ? '—' : substr($time, 0, 5);
}

function mask_cpf(string $cpf): string
{
    $digits = preg_replace('/\D/', '', $cpf);

    if ($digits === null || strlen($digits) !== 11) {
        return '***.***.***-**';
    }

    return '***.***.' . substr($digits, 6, 3) . '-' . substr($digits, 9, 2);
}

function is_admin(): bool
{
    return Auth::hasAnyRole(['administrador']);
}
