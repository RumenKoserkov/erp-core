<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    public function allByCompany(int $companyId, string $search = ''): array
    {
        $sql = "
            SELECT
                products.*,
                categories.name AS category_name,
                suppliers.name AS supplier_name
            FROM products
            INNER JOIN categories ON products.category_id = categories.id
            LEFT JOIN suppliers ON products.supplier_id = suppliers.id
            WHERE products.company_id = ?
        ";

        $params = [$companyId];

        if ($search !== '') {
            $sql .= "
                AND (
                    products.name LIKE ?
                    OR products.internal_code LIKE ?
                    OR products.barcode LIKE ?
                    OR categories.name LIKE ?
                    OR suppliers.name LIKE ?
                )
            ";

            $searchTerm = '%' . $search . '%';

            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY products.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function generateNextInternalCode(int $companyId): string
    {
        $stmt = $this->db->prepare("
            SELECT internal_code
            FROM products
            WHERE company_id = ?
            ORDER BY id DESC
            LIMIT 1
        ");

        $stmt->execute([$companyId]);

        $lastProduct = $stmt->fetch();

        if (!$lastProduct) {
            return 'PRD-000001';
        }

        $lastCode = $lastProduct['internal_code'];
        $number = (int)str_replace('PRD-', '', $lastCode);
        $nextNumber = $number + 1;

        return 'PRD-' . str_pad((string)$nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function barcodeExistsInCompany(string $barcode, int $companyId): bool
    {
        if ($barcode === '') {
            return false;
        }

        $stmt = $this->db->prepare("
            SELECT id
            FROM products
            WHERE barcode = ?
            AND company_id = ?
            LIMIT 1
        ");

        $stmt->execute([$barcode, $companyId]);

        return $stmt->fetch() !== false;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO products
                (
                    company_id,
                    category_id,
                    supplier_id,
                    internal_code,
                    barcode,
                    name,
                    unit,
                    purchase_price,
                    selling_price,
                    min_stock,
                    description,
                    image_path,
                    is_active
                )
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['company_id'],
            $data['category_id'],
            $data['supplier_id'],
            $data['internal_code'],
            $data['barcode'],
            $data['name'],
            $data['unit'],
            $data['purchase_price'],
            $data['selling_price'],
            $data['min_stock'],
            $data['description'],
            $data['image_path'],
            $data['is_active'],
        ]);
    }
}