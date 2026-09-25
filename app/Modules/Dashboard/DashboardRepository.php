<?php

declare(strict_types=1);

namespace Clinica\Modules\Dashboard;

use Clinica\Core\Database;

final class DashboardRepository
{
    /** @return array{medicos: int, pacientes: int, consultas_hoje: int, consultas_futuras: int} */
    public function statistics(): array
    {
        $today = date('Y-m-d');
        $now = date('H:i:s');
        $row = Database::one(
            'SELECT
                (SELECT COUNT(*) FROM medico) AS medicos,
                (SELECT COUNT(*) FROM paciente) AS pacientes,
                (SELECT COUNT(*) FROM consulta WHERE data_consulta = ?) AS consultas_hoje,
                (SELECT COUNT(*) FROM consulta
                 WHERE data_consulta > ? OR (data_consulta = ? AND hora_consulta >= ?)) AS consultas_futuras',
            'ssss',
            [$today, $today, $today, $now],
        ) ?? [];

        return [
            'medicos' => (int) ($row['medicos'] ?? 0),
            'pacientes' => (int) ($row['pacientes'] ?? 0),
            'consultas_hoje' => (int) ($row['consultas_hoje'] ?? 0),
            'consultas_futuras' => (int) ($row['consultas_futuras'] ?? 0),
        ];
    }

    /** @return list<array<string, mixed>> */
    public function nextAppointments(int $limit = 5): array
    {
        $today = date('Y-m-d');
        $now = date('H:i:s');

        return Database::select(
            'SELECT c.id_consulta, c.data_consulta, c.hora_consulta,
                    m.nome_medico, p.nome_paciente
             FROM consulta c
             INNER JOIN medico m ON m.id_medico = c.medico_id_medico
             INNER JOIN paciente p ON p.id_paciente = c.paciente_id_paciente
             WHERE c.data_consulta > ? OR (c.data_consulta = ? AND c.hora_consulta >= ?)
             ORDER BY c.data_consulta, c.hora_consulta, c.id_consulta
             LIMIT ?',
            'sssi',
            [$today, $today, $now, $limit],
        );
    }
}
