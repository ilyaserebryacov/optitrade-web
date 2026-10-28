<?php

namespace App\Routing;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[strtoupper($method)][$path] = $handler;
    }

    public function dispatch(string $method, string $path): mixed
    {
        $method = strtoupper($method);
        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            return [
                'error' => 'Not Found',
                'message' => "Route {$method} {$path} does not exist",
            ];
        }

        // Если передан массив вида [Контроллер::class, 'метод']
        if (is_array($handler)) {
            [$controllerClass, $action] = $handler;
            $controller = new $controllerClass();
            return $controller->$action();
        }

        // Если передана анонимная функция
        return call_user_func($handler);
    }
}
