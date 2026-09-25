<?php

declare(strict_types=1);

namespace Clinica\Core;

use DateTimeImmutable;

final class Validator
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, list<string>> $rules
     * @param array<string, string> $labels
     * @return array<string, list<string>>
     */
    public static function validate(array $data, array $rules, array $labels = []): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $label = $labels[$field] ?? $field;
            $nullable = in_array('nullable', $fieldRules, true);

            if ($nullable && ($value === null || $value === '')) {
                continue;
            }

            foreach ($fieldRules as $rule) {
                if ($rule === 'nullable') {
                    continue;
                }

                [$name, $argument] = array_pad(explode(':', $rule, 2), 2, null);
                $message = self::check($name, $argument, $value, $label);

                if ($message !== null) {
                    $errors[$field][] = $message;
                    break;
                }
            }
        }

        return $errors;
    }

    private static function check(string $rule, ?string $argument, mixed $value, string $label): ?string
    {
        return match ($rule) {
            'required' => $value === null || $value === '' ? "O campo {$label} é obrigatório." : null,
            'string' => !is_string($value) ? "O campo {$label} deve ser um texto válido." : null,
            'integer' => filter_var($value, FILTER_VALIDATE_INT) === false
                ? "O campo {$label} deve ser um número inteiro válido."
                : null,
            'positive' => filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 1
                ? "O campo {$label} deve ser maior que zero."
                : null,
            'max' => is_string($value) && self::length($value) > (int) $argument
                ? "O campo {$label} deve ter no máximo {$argument} caracteres."
                : null,
            'email' => is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) === false
                ? "O campo {$label} deve conter um e-mail válido."
                : null,
            'date' => !self::matchesDate($value, 'Y-m-d')
                ? "O campo {$label} deve conter uma data válida."
                : null,
            'time' => !self::matchesTime($value)
                ? "O campo {$label} deve conter um horário válido."
                : null,
            'in' => !is_scalar($value) || !in_array((string) $value, explode(',', (string) $argument), true)
                ? "O campo {$label} possui uma opção inválida."
                : null,
            'cpf' => !is_string($value) || !self::validCpf($value)
                ? "O campo {$label} deve conter um CPF válido."
                : null,
            default => "A regra de validação {$rule} não está configurada.",
        };
    }

    private static function matchesDate(mixed $value, string $format): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $date = DateTimeImmutable::createFromFormat('!' . $format, $value);

        return $date !== false && $date->format($format) === $value;
    }

    private static function matchesTime(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        foreach (['H:i', 'H:i:s'] as $format) {
            $time = DateTimeImmutable::createFromFormat('!' . $format, $value);

            if ($time !== false && $time->format($format) === $value) {
                return true;
            }
        }

        return false;
    }

    private static function validCpf(string $value): bool
    {
        $cpf = preg_replace('/\D/', '', $value);

        if ($cpf === null || strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($digit = 9; $digit < 11; $digit++) {
            $sum = 0;

            for ($index = 0; $index < $digit; $index++) {
                $sum += (int) $cpf[$index] * (($digit + 1) - $index);
            }

            $check = (10 * $sum) % 11;
            $check = $check === 10 ? 0 : $check;

            if ((int) $cpf[$digit] !== $check) {
                return false;
            }
        }

        return true;
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }
}
