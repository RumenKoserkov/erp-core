CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    company_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    supplier_id BIGINT UNSIGNED NULL,

    internal_code VARCHAR(50) NOT NULL,
    barcode VARCHAR(100) NULL,

    name VARCHAR(255) NOT NULL,
    unit VARCHAR(30) NOT NULL,

    purchase_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    selling_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    min_stock DECIMAL(12,3) NOT NULL DEFAULT 0.000,

    description TEXT NULL,
    image_path VARCHAR(255) NULL,

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_company_product_code (company_id, internal_code),
    UNIQUE KEY unique_company_product_barcode (company_id, barcode),

    CONSTRAINT fk_products_company_id
        FOREIGN KEY (company_id)
        REFERENCES companies(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_products_category_id
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_products_supplier_id
        FOREIGN KEY (supplier_id)
        REFERENCES suppliers(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;