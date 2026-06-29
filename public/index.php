<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

$routes = require_once __DIR__ . '/../routes/web.php';

$router = new Router($routes);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === false) {
    $uri = '/';
}

$router->dispatch($uri);



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