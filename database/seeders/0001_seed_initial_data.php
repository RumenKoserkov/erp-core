<?php

declare(strict_types=1);


return function (PDO $pdo): void {
    $stmt = $pdo->prepare("
        INSERT INTO companies 
            (name, eik, vat_number, phone, email, address, currency, is_active)
        VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        'Demo Company Ltd.',
        '123456789',
        'BG123456789',
        '+359888123456',
        'office@demo-company.com',
        'Sofia, Bulgaria',
        'BGN',
        1
    ]);

    $companyId = (int)$pdo->lastInsertId();

    $roles = [
        [
            'name' => 'Administrator',
            'slug' => 'administrator'
        ],
        [
            'name' => 'Manager',
            'slug' => 'manager'
        ],
        [
            'name' => 'Employee',
            'slug' => 'employee'
        ],
    ];

    foreach ($roles as $role) {
        $stmt = $pdo->prepare("
            INSERT INTO roles 
                (name, slug)
            VALUES 
                (?, ?)
        ");

        $stmt->execute([
            $role['name'],
            $role['slug']
        ]);
    }

    $stmt = $pdo->prepare("SELECT id FROM roles WHERE slug = ?");
    $stmt->execute(['administrator']);

    $administratorRole = $stmt->fetch();

    if (!$administratorRole) {
        throw new Exception('Administrator role was not found.');
    }

    $administratorRoleId = (int)$administratorRole['id'];

    $adminPassword = password_hash('password', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users 
            (company_id, role_id, name, email, password, is_active)
        VALUES 
            (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $companyId,
        $administratorRoleId,
        'System Administrator',
        'admin@example.com',
        $adminPassword,
        1
    ]);

    $settings = [
        [
            'key' => 'company_name',
            'value' => 'Demo Company Ltd.'
        ],
        [
            'key' => 'currency',
            'value' => 'EUR'
        ],
        [
            'key' => 'vat_rate',
            'value' => '20'
        ],
        [
            'key' => 'date_format',
            'value' => 'd.m.Y'
        ],
        [
            'key' => 'invoice_prefix',
            'value' => 'INV'
        ],
    ];

    foreach ($settings as $setting) {
        $stmt = $pdo->prepare("
            INSERT INTO settings 
                (company_id, setting_key, setting_value)
            VALUES 
                (?, ?, ?)
        ");

        $stmt->execute([
            $companyId,
            $setting['key'],
            $setting['value']
        ]);
    }
};