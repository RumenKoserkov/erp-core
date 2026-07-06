<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    public function allByCompany(int $companyId, string $search = ''): array
    {
        if ($search !== '') {
            $searchTerm = '%' . $search . '%';

            $stmt = $this->db->prepare("
                SELECT *
                FROM categories
                WHERE company_id = ?
                AND (
                    name LIKE ?
                    OR description LIKE ?
                )
                ORDER BY id DESC
            ");

            $stmt->execute([
                $companyId,
                $searchTerm,
                $searchTerm,
            ]);

            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare("
            SELECT *
            FROM categories
            WHERE company_id = ?
            ORDER BY id DESC
        ");

        $stmt->execute([$companyId]);

        return $stmt->fetchAll();
    }

    public function findByIdAndCompany(int $id, int $companyId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM categories
            WHERE id = ?
            AND company_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $id,
            $companyId,
        ]);

        $category = $stmt->fetch();

        if (!$category) {
            return null;
        }

        return $category;
    }

    public function nameExistsInCompany(string $name, int $companyId): bool
    {
        $stmt = $this->db->prepare("
            SELECT id
            FROM categories
            WHERE name = ?
            AND company_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $name,
            $companyId,
        ]);

        $category = $stmt->fetch();

        return $category !== false;
    }

    public function nameExistsInCompanyExceptCategory(
        string $name,
        int $companyId,
        int $categoryId
    ): bool {
        $stmt = $this->db->prepare("
            SELECT id
            FROM categories
            WHERE name = ?
            AND company_id = ?
            AND id != ?
            LIMIT 1
        ");

        $stmt->execute([
            $name,
            $companyId,
            $categoryId,
        ]);

        $category = $stmt->fetch();

        return $category !== false;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO categories
                (
                    company_id,
                    name,
                    description,
                    is_active
                )
            VALUES
                (?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['company_id'],
            $data['name'],
            $data['description'],
            $data['is_active'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE categories
            SET
                name = ?,
                description = ?,
                is_active = ?
            WHERE id = ?
            AND company_id = ?
        ");

        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['is_active'],
            $id,
            $data['company_id'],
        ]);
    }

    public function deactivate(int $id, int $companyId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE categories
            SET is_active = 0
            WHERE id = ?
            AND company_id = ?
        ");

        return $stmt->execute([
            $id,
            $companyId,
        ]);
    }

    public function activeByCompany(int $companyId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name
            FROM categories
            WHERE company_id = ?
            AND is_active = 1
            ORDER BY name ASC
        ");

        $stmt->execute([$companyId]);

        return $stmt->fetchAll();
    }
}