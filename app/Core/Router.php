<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(string $uri): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $requestMethod) {
                $controllerClass = $route['action'][0];
                $method = $route['action'][1];

                $controller = new $controllerClass();

                $controller->$method();

                return;
            }
        }

        http_response_code(404);

        echo '404 - Page not found';
    }
}


/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
|
| Router-ът е една от основните части на MVC архитектурата.
|
| Неговата задача е да приеме текущата HTTP заявка (Request),
| да сравни URL адреса и HTTP метода с всички дефинирани маршрути
| (routes) и да определи кой Controller и кой метод трябва да
| обработят заявката.
|
| Основни отговорности:
| - Съхранява всички дефинирани маршрути.
| - Получава текущия URL и HTTP метода.
| - Търси съвпадение между заявката и route-овете.
| - Създава необходимия Controller.
| - Извиква съответния метод (Action).
| - Връща 404, ако няма намерен маршрут.
|
| Поток на изпълнение:
|
| Browser Request
|        ↓
| public/index.php
|        ↓
| routes/web.php
|        ↓
| Router
|        ↓
| Сравнява URI + HTTP Method
|        ↓
| Създава Controller
|        ↓
| Извиква Action
|        ↓
| Controller
|        ↓
| View
|        ↓
| HTML Response
|
| Пример:
|
| GET /dashboard
|        ↓
| DashboardController::index()
|
| GET /products
|        ↓
| ProductController::index()
|
| GET /sales
|        ↓
| SaleController::index()
|
| Router-ът не съдържа бизнес логика и не работи с базата данни.
| Единствената му задача е да насочи заявката към правилния
| Controller.
|
*/