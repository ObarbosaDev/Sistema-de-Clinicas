<?php

declare(strict_types=1);

namespace Clinica\Core;

final class Auth
{
    private const SESSION_KEY = 'auth_user';

    public static function check(): bool
    {
        $user = Session::get(self::SESSION_KEY);

        return is_array($user) && isset($user['id'], $user['name'], $user['role']);
    }

    /** @return array{id: int, name: string, role: string}|null */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        /** @var array{id: int, name: string, role: string} $user */
        $user = Session::get(self::SESSION_KEY);

        return $user;
    }

    public static function login(int $id, string $name, string $role): void
    {
        Session::regenerate();
        Session::put(self::SESSION_KEY, [
            'id' => $id,
            'name' => $name,
            'role' => $role,
        ]);
    }

    public static function logout(): void
    {
        Session::invalidate();
    }

    /** @param list<string> $roles */
    public static function hasAnyRole(array $roles): bool
    {
        $user = self::user();

        return $user !== null && in_array($user['role'], $roles, true);
    }
}
