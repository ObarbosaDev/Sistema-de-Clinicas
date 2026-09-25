<?php

declare(strict_types=1);

namespace Clinica\Core;

final class Session
{
    /** @param array<string, mixed> $config */
    public static function start(array $config): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');

        session_name((string) $config['name']);
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => (bool) $config['secure'],
            'httponly' => true,
            'samesite' => (string) $config['same_site'],
        ]);

        session_start();
        self::ageFlashData();
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash_next'][$key] = $value;
    }

    public static function flashed(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash_current'][$key] ?? $default;
    }

    public static function invalidate(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
    }

    private static function ageFlashData(): void
    {
        $_SESSION['_flash_current'] = $_SESSION['_flash_next'] ?? [];
        unset($_SESSION['_flash_next']);
    }
}
