<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../../resources/views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../../resources/views/layouts/main.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo 'View not found: ' . htmlspecialchars($view);
            return;
        }

        if (!file_exists($layoutPath)) {
            http_response_code(500);
            echo 'Layout not found.';
            return;
        }

        ob_start();

        require $viewPath;

        $content = ob_get_clean();

        require $layoutPath;
    }
}