<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function abort(int $statusCode): void
    {
        http_response_code($statusCode);

        if ($statusCode === 403) {
            View::render('errors/403', [
                'title' => '403 - Access denied'
            ]);
            exit;
        }

        if ($statusCode === 404) {
            View::render('errors/404', [
                'title' => '404 - Page not found'
            ]);
            exit;
        }

        View::render('errors/500', [
            'title' => '500 - Server error'
        ]);

        exit;
    }
}