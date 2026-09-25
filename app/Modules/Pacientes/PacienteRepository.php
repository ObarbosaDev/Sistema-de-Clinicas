<?php

declare(strict_types=1);

namespace Clinica\Modules\Pacientes;

use Clinica\Core\Database;

final class PacienteRepository
{
    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        return Database::select(
            'SELECT id_paciente, nome_paciente, cpf_paciente, dt_nasc_paciente,
                    sexo_paciente, endereco_paciente, fone_paciente, email_paciente
             FROM paciente
             ORDER BY nome_paciente, id_paciente',
        );
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        return Database::one(
            'SELECT id_paciente, nome_paciente, cpf_paciente, dt_nasc_paciente,
                    sexo_paciente, endereco_paciente, fone_paciente, email_paciente
             FROM paciente
             WHERE id_paciente = ?',
            'i',
            [$id],
        );
    }

    /** @param array<string, string> $data */
    public function create(array $data): int
    {
        return Database::insert(
            'INSERT INTO paciente (
                nome_paciente, cpf_paciente, dt_nasc_paciente, sexo_paciente,
                endereco_paciente, fone_paciente, email_paciente
             ) VALUES (?, ?, ?, ?, NULLIF(?, \'\'), NULLIF(?, \'\'), NULLIF(?, \'\'))',
            'sssssss',
            [
                $data['nome_paciente'],
                $data['cpf_paciente'],
                $data['dt_nasc_paciente'],
                $data['sexo_paciente'],
                $data['endereco_paciente'],
                $data['fone_paciente'],
                $data['email_paciente'],
            ],
        );
    }

    /** @param array<string, string> $data */
    public function update(int $id, array $data): int
    {
        return Database::execute(
            'UPDATE paciente SET
                nome_paciente = ?, cpf_paciente = ?, dt_nasc_paciente = ?, sexo_paciente = ?,
                endereco_paciente = NULLIF(?, \'\'), fone_paciente = NULLIF(?, \'\'),
                email_paciente = NULLIF(?, \'\')
             WHERE id_paciente = ?',
            'sssssssi',
            [
                $data['nome_paciente'],
                $data['cpf_paciente'],
                $data['dt_nasc_paciente'],
                $data['sexo_paciente'],
                $data['endereco_paciente'],
                $data['fone_paciente'],
                $data['email_paciente'],
                $id,
            ],
        );
    }

    public function delete(int $id): int
    {
        return Database::execute('DELETE FROM paciente WHERE id_paciente = ?', 'i', [$id]);
    }

    public function cpfExists(string $cpf, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT EXISTS(SELECT 1 FROM paciente WHERE cpf_paciente = ?';
        $types = 's';
        $parameters = [$cpf];

        if ($ignoreId !== null) {
            $sql .= ' AND id_paciente <> ?';
            $types .= 'i';
            $parameters[] = $ignoreId;
        }

        $row = Database::one($sql . ') AS existe', $types, $parameters);

        return (bool) ($row['existe'] ?? false);
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        if ($email === '') {
            return false;
        }

        $sql = 'SELECT EXISTS(SELECT 1 FROM paciente WHERE email_paciente = ?';
        $types = 's';
        $parameters = [$email];

        if ($ignoreId !== null) {
            $sql .= ' AND id_paciente <> ?';
            $types .= 'i';
            $parameters[] = $ignoreId;
        }

        $row = Database::one($sql . ') AS existe', $types, $parameters);

        return (bool) ($row['existe'] ?? false);
    }

    public function hasAppointments(int $id): bool
    {
        $row = Database::one(
            'SELECT EXISTS(SELECT 1 FROM consulta WHERE paciente_id_paciente = ?) AS possui_consultas',
            'i',
            [$id],
        );

        return (bool) ($row['possui_consultas'] ?? false);
    }
}
