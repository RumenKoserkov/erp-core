CREATE TABLE warehouse_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    company_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,

    from_warehouse_id BIGINT UNSIGNED NULL,
    to_warehouse_id BIGINT UNSIGNED NULL,

    user_id BIGINT UNSIGNED NULL,

    type VARCHAR(50) NOT NULL,

    quantity DECIMAL(12,3) NOT NULL,

    reference_type VARCHAR(100) NULL,
    reference_id BIGINT UNSIGNED NULL,

    note TEXT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_warehouse_transactions_company_id
        FOREIGN KEY (company_id)
        REFERENCES companies(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_warehouse_transactions_product_id
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_warehouse_transactions_from_warehouse_id
        FOREIGN KEY (from_warehouse_id)
        REFERENCES warehouses(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_warehouse_transactions_to_warehouse_id
        FOREIGN KEY (to_warehouse_id)
        REFERENCES warehouses(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_warehouse_transactions_user_id
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;