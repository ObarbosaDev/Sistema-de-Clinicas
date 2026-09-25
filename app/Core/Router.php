<?php

declare(strict_types=1);

namespace Clinica\Core;

final class Router
{
    /** @var array<string, array<string, array{handler: callable|array{class-string, string}, auth: bool, roles: list<string>}>> */
    private array $routes = [];

    /** @param callable|array{class-string, string} $handler @param list<string> $roles */
    public function get(string $path, callable|array $handler, bool $auth = true, array $roles = []): self
    {
        return $this->add('GET', $path, $handler, $auth, $roles);
    }

    /** @param callable|array{class-string, string} $handler @param list<string> $roles */
    public function post(string $path, callable|array $handler, bool $auth = true, array $roles = []): self
    {
        return $this->add('POST', $path, $handler, $auth, $roles);
    }

    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        $path = trim($path, '/');
        $route = $this->routes[$method][$path] ?? null;

        if ($route === null) {
            foreach ($this->routes as $routesByMethod) {
                if (isset($routesByMethod[$path])) {
                    throw new HttpException(405, 'Método HTTP não permitido para esta rota.');
                }
            }

            throw new HttpException(404, 'A página solicitada não foi encontrada.');
        }

        if ($route['auth'] && !Auth::check()) {
            \redirect('login');
        }

        if ($route['roles'] !== [] && !Auth::hasAnyRole($route['roles'])) {
            throw new HttpException(403, 'Você não possui permissão para realizar esta ação.');
        }

        if ($method === 'POST' && !Csrf::validate($_POST['_token'] ?? null)) {
            throw new HttpException(419, 'Sua sessão expirou. Atualize a página e tente novamente.');
        }

        $handler = $route['handler'];

        if (is_array($handler) && is_string($handler[0])) {
            $controller = new $handler[0]();
            $controller->{$handler[1]}();

            return;
        }

        $handler();
    }

    /** @param callable|array{class-string, string} $handler @param list<string> $roles */
    private function add(string $method, string $path, callable|array $handler, bool $auth, array $roles): self
    {
        $this->routes[$method][trim($path, '/')] = [
            'handler' => $handler,
            'auth' => $auth,
            'roles' => $roles,
        ];

        return $this;
    }
}
