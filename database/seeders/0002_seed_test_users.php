<?php

declare(strict_types=1);


return function (PDO $pdo): void {
    $stmt = $pdo->prepare("SELECT id FROM companies WHERE name = ? LIMIT 1");
    $stmt->execute(['Demo Company Ltd.']);

    $company = $stmt->fetch();

    if (!$company) {
        throw new Exception('Demo company was not found.');
    }

    $companyId = (int)$company['id'];

    $stmt = $pdo->prepare("SELECT id FROM roles WHERE slug = ? LIMIT 1");
    $stmt->execute(['manager']);

    $managerRole = $stmt->fetch();

    if (!$managerRole) {
        throw new Exception('Manager role was not found.');
    }

    $managerRoleId = (int)$managerRole['id'];

    $stmt = $pdo->prepare("SELECT id FROM roles WHERE slug = ? LIMIT 1");
    $stmt->execute(['employee']);

    $employeeRole = $stmt->fetch();

    if (!$employeeRole) {
        throw new Exception('Employee role was not found.');
    }

    $employeeRoleId = (int)$employeeRole['id'];

    $password = password_hash('password', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users 
            (company_id, role_id, name, email, password, is_active)
        VALUES 
            (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $companyId,
        $managerRoleId,
        'Demo Manager',
        'manager@example.com',
        $password,
        1
    ]);

    $stmt = $pdo->prepare("
        INSERT INTO users 
            (company_id, role_id, name, email, password, is_active)
        VALUES 
            (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $companyId,
        $employeeRoleId,
        'Demo Employee',
        'employee@example.com',
        $password,
        1
    ]);
};