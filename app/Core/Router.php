<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->add('GET', $uri, $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->add('POST', $uri, $action);
    }

    public function add(string $method, string $uri, array $action): void
    {
        $this->routes[strtoupper($method)][$uri] = $action;
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $path = parse_url($requestUri, PHP_URL_PATH);

        $requestMethod = strtoupper($requestMethod);

        if (!isset($this->routes[$requestMethod][$path])) {
            http_response_code(404);

            echo '404 - Page not found';

            return;
        }

        [$controllerClass, $method] = $this->routes[$requestMethod][$path];

        $controller = new $controllerClass();

        $controller->$method();
    }
}