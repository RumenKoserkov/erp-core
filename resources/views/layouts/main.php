<?php

declare(strict_types=1);

use App\Services\AuthService;

$content = $content ?? '';

$authService = new AuthService();

$currentUser = $authService->user();

?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">

    <title><?= htmlspecialchars($title ?? 'Warehouse ERP') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <a class="navbar-brand" href="/dashboard">
            Warehouse ERP
        </a>

        <?php if ($currentUser !== null): ?>

            <div class="d-flex align-items-center">

                <ul class="navbar-nav flex-row me-4">

                    <li class="nav-item me-3">
                        <a href="/dashboard" class="nav-link">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item me-3">
                        <a href="/products" class="nav-link">
                            Products
                        </a>
                    </li>

                    <li class="nav-item me-3">
                        <a href="/warehouses" class="nav-link">
                            Warehouses
                        </a>
                    </li>

                    <li class="nav-item me-3">
                        <a href="/sales" class="nav-link">
                            Sales
                        </a>
                    </li>

                    <li class="nav-item me-4">
                        <a href="/purchases" class="nav-link">
                            Purchases
                        </a>
                    </li>

                </ul>

                <span class="text-white me-3">
                    <?= htmlspecialchars($currentUser['name']) ?>
                    |
                    <?= htmlspecialchars($currentUser['role_name']) ?>
                </span>

                <form action="/logout" method="POST" class="mb-0">
                    <button
                        type="submit"
                        class="btn btn-outline-light btn-sm"
                    >
                        Logout
                    </button>
                </form>

            </div>

        <?php else: ?>

            <a href="/login" class="btn btn-outline-light btn-sm">
                Login
            </a>

        <?php endif; ?>

    </div>
</nav>

<main class="container">
    <?= $content ?>
</main>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>