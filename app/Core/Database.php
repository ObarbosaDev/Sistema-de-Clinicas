<?php

declare(strict_types=1);

namespace Clinica\Core;

use InvalidArgumentException;
use mysqli;
use mysqli_stmt;
use RuntimeException;

final class Database
{
    /** @var array<string, mixed> */
    private static array $config = [];
    private static ?mysqli $connection = null;

    /** @param array<string, mixed> $config */
    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    public static function connection(): mysqli
    {
        if (self::$connection instanceof mysqli) {
            return self::$connection;
        }

        if (self::$config === []) {
            throw new RuntimeException('A conexão com o banco não foi configurada.');
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $connection = new mysqli(
            (string) self::$config['host'],
            (string) self::$config['username'],
            (string) self::$config['password'],
            (string) self::$config['name'],
            (int) self::$config['port'],
        );
        $connection->set_charset((string) self::$config['charset']);

        self::$connection = $connection;

        return self::$connection;
    }

    /**
     * @param list<mixed> $parameters
     * @return list<array<string, mixed>>
     */
    public static function select(string $sql, string $types = '', array $parameters = []): array
    {
        $statement = self::prepareAndExecute($sql, $types, $parameters);
        $result = $statement->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $statement->close();

        return $rows;
    }

    /**
     * @param list<mixed> $parameters
     * @return array<string, mixed>|null
     */
    public static function one(string $sql, string $types = '', array $parameters = []): ?array
    {
        $rows = self::select($sql, $types, $parameters);

        return $rows[0] ?? null;
    }

    /** @param list<mixed> $parameters */
    public static function execute(string $sql, string $types = '', array $parameters = []): int
    {
        $statement = self::prepareAndExecute($sql, $types, $parameters);
        $affectedRows = $statement->affected_rows;
        $statement->close();

        return $affectedRows;
    }

    /** @param list<mixed> $parameters */
    public static function insert(string $sql, string $types = '', array $parameters = []): int
    {
        $statement = self::prepareAndExecute($sql, $types, $parameters);
        $statement->close();

        return self::connection()->insert_id;
    }

    /** @param list<mixed> $parameters */
    private static function prepareAndExecute(string $sql, string $types, array $parameters): mysqli_stmt
    {
        if (strlen($types) !== count($parameters)) {
            throw new InvalidArgumentException('Tipos e parâmetros SQL possuem tamanhos diferentes.');
        }

        $statement = self::connection()->prepare($sql);

        if ($types !== '') {
            $values = array_values($parameters);
            $references = [];

            foreach ($values as $index => &$value) {
                $references[$index] = &$value;
            }
            unset($value);

            $statement->bind_param($types, ...$references);
        }

        $statement->execute();

        return $statement;
    }
}
