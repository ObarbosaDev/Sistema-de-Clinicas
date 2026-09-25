<?php

declare(strict_types=1);

namespace Clinica\Modules\Consultas;

use Clinica\Core\Database;

final class ConsultaRepository
{
    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        return Database::select(
            'SELECT c.id_consulta, c.data_consulta, c.hora_consulta, c.descricao_consulta,
                    m.id_medico, m.nome_medico, m.crm_medico,
                    p.id_paciente, p.nome_paciente
             FROM consulta c
             INNER JOIN medico m ON m.id_medico = c.medico_id_medico
             INNER JOIN paciente p ON p.id_paciente = c.paciente_id_paciente
             ORDER BY c.data_consulta DESC, c.hora_consulta DESC, c.id_consulta DESC',
        );
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        return Database::one(
            'SELECT id_consulta, data_consulta, hora_consulta, descricao_consulta,
                    medico_id_medico, paciente_id_paciente
             FROM consulta
             WHERE id_consulta = ?',
            'i',
            [$id],
        );
    }

    /** @return list<array<string, mixed>> */
    public function doctors(): array
    {
        return Database::select(
            'SELECT id_medico, nome_medico, crm_medico FROM medico ORDER BY nome_medico, id_medico',
        );
    }

    /** @return list<array<string, mixed>> */
    public function patients(): array
    {
        return Database::select(
            'SELECT id_paciente, nome_paciente, cpf_paciente FROM paciente ORDER BY nome_paciente, id_paciente',
        );
    }

    public function doctorExists(int $id): bool
    {
        $row = Database::one('SELECT EXISTS(SELECT 1 FROM medico WHERE id_medico = ?) AS existe', 'i', [$id]);

        return (bool) ($row['existe'] ?? false);
    }

    public function patientExists(int $id): bool
    {
        $row = Database::one('SELECT EXISTS(SELECT 1 FROM paciente WHERE id_paciente = ?) AS existe', 'i', [$id]);

        return (bool) ($row['existe'] ?? false);
    }

    /** @return array<string, mixed>|null */
    public function findConflict(
        int $doctorId,
        int $patientId,
        string $date,
        string $time,
        ?int $ignoreId = null,
    ): ?array {
        $sql = 'SELECT c.id_consulta, c.medico_id_medico, c.paciente_id_paciente,
                       m.nome_medico, p.nome_paciente
                FROM consulta c
                INNER JOIN medico m ON m.id_medico = c.medico_id_medico
                INNER JOIN paciente p ON p.id_paciente = c.paciente_id_paciente
                WHERE c.data_consulta = ? AND c.hora_consulta = ?
                  AND (c.medico_id_medico = ? OR c.paciente_id_paciente = ?)';
        $types = 'ssii';
        $parameters = [$date, $time, $doctorId, $patientId];

        if ($ignoreId !== null) {
            $sql .= ' AND c.id_consulta <> ?';
            $types .= 'i';
            $parameters[] = $ignoreId;
        }

        $sql .= ' LIMIT 1';

        return Database::one($sql, $types, $parameters);
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO consulta (
                paciente_id_paciente, medico_id_medico, data_consulta, hora_consulta, descricao_consulta
             ) VALUES (?, ?, ?, ?, NULLIF(?, \'\'))',
            'iisss',
            [
                $data['paciente_id_paciente'],
                $data['medico_id_medico'],
                $data['data_consulta'],
                $data['hora_consulta'],
                $data['descricao_consulta'],
            ],
        );
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): int
    {
        return Database::execute(
            'UPDATE consulta SET
                paciente_id_paciente = ?, medico_id_medico = ?, data_consulta = ?,
                hora_consulta = ?, descricao_consulta = NULLIF(?, \'\')
             WHERE id_consulta = ?',
            'iisssi',
            [
                $data['paciente_id_paciente'],
                $data['medico_id_medico'],
                $data['data_consulta'],
                $data['hora_consulta'],
                $data['descricao_consulta'],
                $id,
            ],
        );
    }

    public function delete(int $id): int
    {
        return Database::execute('DELETE FROM consulta WHERE id_consulta = ?', 'i', [$id]);
    }
}
