<?php

declare(strict_types=1);

namespace Clinica\Core;

use RuntimeException;
use Throwable;

final class View
{
    /** @param array<string, mixed> $data */
    public static function render(string $view, array $data = [], string $layout = 'app'): void
    {
        $viewPath = self::resolve($view);
        $layoutPath = self::resolve('Shared/Views/layouts/' . $layout);

        extract($data, EXTR_SKIP);

        ob_start();

        try {
            require $viewPath;
            $content = (string) ob_get_clean();
        } catch (Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }

        require $layoutPath;
    }

    private static function resolve(string $relativePath): string
    {
        if (str_contains($relativePath, '..')) {
            throw new RuntimeException('Caminho de view inválido.');
        }

        $path = BASE_PATH . '/app/' . trim($relativePath, '/') . '.php';

        if (!is_file($path)) {
            throw new RuntimeException('View não encontrada: ' . $relativePath);
        }

        return $path;
    }
}
