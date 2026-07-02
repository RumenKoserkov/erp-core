<?php

declare(strict_types=1);

use App\Core\Router;
use App\Core\View;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $router = new Router();

    $routes = require_once __DIR__ . '/../routes/web.php';

    foreach ($routes as $route) {
        $router->add(
            $route['method'],
            $route['uri'],
            $route['action'],
            $route['middleware'] ?? []
        );
    }

    $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Throwable $exception) {
    http_response_code(500);

    View::render('errors/500', [
        'title' => '500 - Server error'
    ]);
}
/*
|--------------------------------------------------------------------------
| Front Controller
|--------------------------------------------------------------------------
|
| Това е входната точка (Entry Point) на цялото приложение.
| Всички HTTP заявки първо преминават през този файл.
|
| Основни задачи:
| - Зарежда Composer Autoloader-а.
| - Зарежда всички дефинирани routes.
| - Създава Router обекта.
| - Извлича текущия URL.
| - Предава заявката към Router-а.
|
| Поток на изпълнение:
|
| Browser Request
|        ↓
| public/index.php
|        ↓
| vendor/autoload.php
|        ↓
| routes/web.php
|        ↓
| Router
|        ↓
| Controller
|        ↓
| View
|        ↓
| HTML Response
|
| Пример:
| GET /dashboard
| ↓
| index.php
| ↓
| Router
| ↓
| DashboardController::index()
| ↓
| resources/views/dashboard/index.php
|
*/