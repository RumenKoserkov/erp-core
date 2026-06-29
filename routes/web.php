<?php

declare(strict_types=1);

use App\Controllers\DashboardController;

return [
    [
        'method' => 'GET',
        'uri' => '/',
        'action' => [DashboardController::class, 'index'],
    ],
    [
        'method' => 'GET',
        'uri' => '/dashboard',
        'action' => [DashboardController::class, 'index'],
    ],
];



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Този файл съдържа всички маршрути (routes) на приложението.
| Всеки route описва:
| - HTTP метода (GET, POST, PUT, DELETE)
| - URL адреса (URI)
| - Controller-а, който ще обработи заявката
| - Метода на Controller-а, който ще бъде извикан
|
| Поток на изпълнение:
| Browser Request
|        ↓
| public/index.php
|        ↓
| routes/web.php
|        ↓
| Router
|        ↓
| Controller@method
|        ↓
| View
|        ↓
| HTML Response
|
| Пример:
| GET /dashboard
| ↓
| DashboardController::index()
|
*/