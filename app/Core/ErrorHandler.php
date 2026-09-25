<?php

declare(strict_types=1);

namespace Clinica\Core;

use ErrorException;
use Throwable;

final class ErrorHandler
{
    private static bool $debug = false;

    /** @param array<string, mixed> $config */
    public static function register(array $config): void
    {
        self::$debug = (bool) ($config['debug'] ?? false);

        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        set_error_handler(static function (
            int $severity,
            string $message,
            string $file,
            int $line,
        ): bool {
            if ((error_reporting() & $severity) === 0) {
                return false;
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler([self::class, 'render']);
    }

    public static function render(Throwable $exception): never
    {
        $status = $exception instanceof HttpException ? $exception->status() : 500;
        $safeMessages = [
            403 => 'Você não possui permissão para acessar este recurso.',
            404 => 'A página solicitada não foi encontrada.',
            405 => 'O método usado não é permitido nesta rota.',
            419 => 'Sua sessão expirou. Atualize a página e tente novamente.',
            500 => 'Não foi possível concluir a solicitação. Tente novamente mais tarde.',
        ];
        $titles = [
            403 => 'Acesso negado',
            404 => 'Página não encontrada',
            405 => 'Método não permitido',
            419 => 'Sessão expirada',
            500 => 'Erro interno',
        ];

        http_response_code($status);

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
            header('Cache-Control: no-store, private');
        }

        self::report($exception);

        $title = $titles[$status] ?? 'Erro';
        $message = $safeMessages[$status] ?? 'Não foi possível concluir a solicitação.';
        $debug = self::$debug;
        $details = $debug ? (string) $exception : '';

        require BASE_PATH . '/app/Shared/Views/errors/error.php';
        exit(1);
    }

    public static function report(Throwable $exception): void
    {
        try {
            $directory = BASE_PATH . '/storage/logs';

            if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
                @error_log((string) $exception);

                return;
            }

            $entry = sprintf(
                "[%s] %s in %s:%d%s%s%s",
                date(DATE_ATOM),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                PHP_EOL,
                $exception->getTraceAsString(),
                PHP_EOL,
            );

            if (!@error_log($entry, 3, $directory . '/app.log')) {
                @error_log((string) $exception);
            }
        } catch (Throwable) {
            @error_log((string) $exception);
        }
    }
}
