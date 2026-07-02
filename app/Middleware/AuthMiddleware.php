<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\AuthService;

class AuthMiddleware
{
    public function handle(array $parameters = []): void
    {
        $authService = new AuthService();

        if (!$authService->check()) {
            header('Location: /login');
            exit;
        }
    }
}