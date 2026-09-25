<?php

declare(strict_types=1);

namespace Clinica\Modules\Auth;

use Clinica\Core\Database;

final class LoginAttemptRepository
{
    public function isBlocked(string $key): bool
    {
        $row = Database::one(
            'SELECT EXISTS(
                SELECT 1 FROM tentativas_login
                WHERE chave_login = ? AND bloqueado_ate > CURRENT_TIMESTAMP
             ) AS bloqueado',
            's',
            [$key],
        );

        return (bool) ($row['bloqueado'] ?? false);
    }

    public function recordFailure(string $key): void
    {
        Database::execute(
            'INSERT INTO tentativas_login (
                chave_login, tentativas, ultima_tentativa_em, bloqueado_ate
             ) VALUES (?, 1, CURRENT_TIMESTAMP, NULL)
             ON DUPLICATE KEY UPDATE
                tentativas = CASE
                    WHEN ultima_tentativa_em < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE)
                         OR bloqueado_ate <= CURRENT_TIMESTAMP THEN 1
                    ELSE LEAST(tentativas + 1, 255)
                END,
                bloqueado_ate = CASE
                    WHEN bloqueado_ate > CURRENT_TIMESTAMP THEN bloqueado_ate
                    WHEN bloqueado_ate <= CURRENT_TIMESTAMP
                         OR ultima_tentativa_em < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE) THEN NULL
                    WHEN tentativas >= 5 THEN DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 5 MINUTE)
                    ELSE NULL
                END,
                ultima_tentativa_em = CURRENT_TIMESTAMP',
            's',
            [$key],
        );
    }

    public function clear(string $key): void
    {
        Database::execute('DELETE FROM tentativas_login WHERE chave_login = ?', 's', [$key]);
    }
}
