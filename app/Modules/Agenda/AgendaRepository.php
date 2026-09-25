<?php

declare(strict_types=1);

namespace Clinica\Modules\Agenda;

use Clinica\Core\Database;

final class AgendaRepository
{
    /** @return list<array<string, mixed>> */
    public function byDate(string $date): array
    {
        return Database::select(
            'SELECT c.id_consulta, c.hora_consulta, c.descricao_consulta,
                    m.id_medico, m.nome_medico, m.crm_medico,
                    p.id_paciente, p.nome_paciente
             FROM consulta c
             INNER JOIN medico m ON m.id_medico = c.medico_id_medico
             INNER JOIN paciente p ON p.id_paciente = c.paciente_id_paciente
             WHERE c.data_consulta = ?
             ORDER BY c.hora_consulta, m.nome_medico, c.id_consulta',
            's',
            [$date],
        );
    }

    /** @return array<string, int> */
    public function countsByDate(string $startDate, string $endDate): array
    {
        $rows = Database::select(
            'SELECT data_consulta, COUNT(*) AS total
             FROM consulta
             WHERE data_consulta >= ? AND data_consulta < ?
             GROUP BY data_consulta
             ORDER BY data_consulta',
            'ss',
            [$startDate, $endDate],
        );
        $counts = [];

        foreach ($rows as $row) {
            $counts[(string) $row['data_consulta']] = (int) $row['total'];
        }

        return $counts;
    }

    /** @return list<array<string, mixed>> */
    public function report(string $startDate, string $endDate, ?int $doctorId): array
    {
        $sql = 'SELECT c.id_consulta, c.data_consulta, c.hora_consulta, c.descricao_consulta,
                       m.id_medico, m.nome_medico, m.crm_medico,
                       p.id_paciente, p.nome_paciente
                FROM consulta c
                INNER JOIN medico m ON m.id_medico = c.medico_id_medico
                INNER JOIN paciente p ON p.id_paciente = c.paciente_id_paciente
                WHERE c.data_consulta BETWEEN ? AND ?';
        $types = 'ss';
        $parameters = [$startDate, $endDate];

        if ($doctorId !== null) {
            $sql .= ' AND m.id_medico = ?';
            $types .= 'i';
            $parameters[] = $doctorId;
        }

        $sql .= ' ORDER BY m.nome_medico, m.id_medico, c.data_consulta, c.hora_consulta, c.id_consulta';

        return Database::select($sql, $types, $parameters);
    }

    /** @return list<array<string, mixed>> */
    public function doctors(): array
    {
        return Database::select(
            'SELECT id_medico, nome_medico, crm_medico FROM medico ORDER BY nome_medico, id_medico',
        );
    }

    public function doctorExists(int $id): bool
    {
        $row = Database::one('SELECT EXISTS(SELECT 1 FROM medico WHERE id_medico = ?) AS existe', 'i', [$id]);

        return (bool) ($row['existe'] ?? false);
    }
}
