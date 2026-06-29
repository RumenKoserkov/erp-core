<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\View;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }
}