<?php
declare(strict_types=1);

namespace Core;

class Router
{
    private array $routes = ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => []];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->routes['DELETE'][$path] = $handler;
    }

    public function load(array $routes): void
    {
        foreach ($routes as $method => $list) {
            foreach ($list as $path => $handler) {
                $this->routes[strtoupper($method)][$path] = $handler;
            }
        }
    }

    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            Response::error('Rota não encontrada: ' . $path, 404);
        }

        if (is_callable($handler) && !is_array($handler)) {
            $handler();
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $action] = $handler;
            if (!class_exists($class)) {
                Response::error('Controller não encontrado.', 500);
            }
            $instance = new $class();
            if (!method_exists($instance, $action)) {
                Response::error('Ação não encontrada.', 500);
            }
            $instance->$action();
            return;
        }

        Response::error('Handler inválido.', 500);
    }
}