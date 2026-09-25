<?php

declare(strict_types=1);

namespace Clinica\Modules\Medicos;

use Clinica\Core\Database;

final class MedicoRepository
{
    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        return Database::select(
            'SELECT id_medico, nome_medico, crm_medico, especialidade_medico
             FROM medico
             ORDER BY nome_medico, id_medico',
        );
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        return Database::one(
            'SELECT id_medico, nome_medico, crm_medico, especialidade_medico
             FROM medico
             WHERE id_medico = ?',
            'i',
            [$id],
        );
    }

    /** @param array{nome_medico: string, crm_medico: string, especialidade_medico: string} $data */
    public function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO medico (nome_medico, crm_medico, especialidade_medico)
             VALUES (?, ?, ?)',
            'sss',
            [$data['nome_medico'], $data['crm_medico'], $data['especialidade_medico']],
        );
    }

    /** @param array{nome_medico: string, crm_medico: string, especialidade_medico: string} $data */
    public function update(int $id, array $data): int
    {
        return Database::execute(
            'UPDATE medico
             SET nome_medico = ?, crm_medico = ?, especialidade_medico = ?
             WHERE id_medico = ?',
            'sssi',
            [$data['nome_medico'], $data['crm_medico'], $data['especialidade_medico'], $id],
        );
    }

    public function delete(int $id): int
    {
        return Database::execute('DELETE FROM medico WHERE id_medico = ?', 'i', [$id]);
    }

    public function hasAppointments(int $id): bool
    {
        $row = Database::one(
            'SELECT EXISTS(SELECT 1 FROM consulta WHERE medico_id_medico = ?) AS possui_consultas',
            'i',
            [$id],
        );

        return (bool) ($row['possui_consultas'] ?? false);
    }
}
