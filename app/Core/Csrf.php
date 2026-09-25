<?php

declare(strict_types=1);

namespace Clinica\Core;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        $token = Session::get(self::SESSION_KEY);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Session::put(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public static function validate(mixed $submittedToken): bool
    {
        $sessionToken = Session::get(self::SESSION_KEY);

        return is_string($submittedToken)
            && is_string($sessionToken)
            && $submittedToken !== ''
            && hash_equals($sessionToken, $submittedToken);
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
}
