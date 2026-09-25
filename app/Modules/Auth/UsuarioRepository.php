<?php

declare(strict_types=1);

namespace Clinica\Modules\Auth;

use Clinica\Core\Database;

final class UsuarioRepository
{
    /** @return array<string, mixed>|null */
    public function findByEmail(string $email): ?array
    {
        return Database::one(
            'SELECT id_usuario, nome_usuario, email_usuario, senha_usuario, perfil_usuario
             FROM usuarios
             WHERE email_usuario = ?
             LIMIT 1',
            's',
            [$email],
        );
    }

    public function updatePasswordHash(int $id, string $hash): void
    {
        Database::execute(
            'UPDATE usuarios SET senha_usuario = ?, atualizado_em = CURRENT_TIMESTAMP WHERE id_usuario = ?',
            'si',
            [$hash, $id],
        );
    }

    public function create(string $name, string $email, string $passwordHash, string $role): int
    {
        return Database::insert(
            'INSERT INTO usuarios (nome_usuario, email_usuario, senha_usuario, perfil_usuario)
             VALUES (?, ?, ?, ?)',
            'ssss',
            [$name, $email, $passwordHash, $role],
        );
    }
}
