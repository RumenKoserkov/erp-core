<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\View;
use App\Services\AuthService;

class RoleMiddleware
{
    public function handle(array $allowedRoles = []): void
    {
        $authService = new AuthService();

        if (!$authService->check()) {
            header('Location: /login');
            exit;
        }

        if (empty($allowedRoles)) {
            $this->denyAccess();
        }

        if (!$authService->hasAnyRole($allowedRoles)) {
            $this->denyAccess();
        }
    }

    private function denyAccess(): void
    {
        http_response_code(403);

        View::render('errors/403', [
            'title' => '403 - Access denied'
        ]);

        exit;
    }
}