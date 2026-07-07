<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Stock Levels</h1>

    <div class="d-flex gap-2">
        <a href="/stock/in" class="btn btn-success">
            Stock In
        </a>

        <a href="/stock/out" class="btn btn-danger">
            Stock Out
        </a>

        <a href="/stock/transfer" class="btn btn-primary">
            Transfer
        </a>
    </div>
</div>

<form method="GET" action="/stock" class="mb-3">
    <div class="input-group">
        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Search by product, code, barcode or warehouse..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <button type="submit" class="btn btn-outline-secondary">
            Search
        </button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($stockLevels)): ?>
            <p class="text-muted mb-0">
                No stock records found.
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Barcode</th>
                            <th>Product</th>
                            <th>Warehouse</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Min Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($stockLevels as $stock): ?>
                            <tr>
                                <td>
                                    <span class="badge text-bg-secondary">
                                        <?= htmlspecialchars($stock['internal_code']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars((string)$stock['barcode']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($stock['product_name']) ?>

                                    <?php if ((int)$stock['product_is_active'] === 0): ?>
                                        <span class="badge text-bg-warning ms-1">
                                            Inactive product
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($stock['warehouse_code'] . ' - ' . $stock['warehouse_name']) ?>

                                    <?php if ((int)$stock['warehouse_is_active'] === 0): ?>
                                        <span class="badge text-bg-warning ms-1">
                                            Inactive warehouse
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars((string)$stock['quantity']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($stock['unit']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars((string)$stock['min_stock']) ?>
                                </td>

                                <td>
                                    <?php if ((float)$stock['quantity'] <= 0): ?>
                                        <span class="badge text-bg-danger">
                                            Out of Stock
                                        </span>
                                    <?php elseif ((float)$stock['quantity'] <= (float)$stock['min_stock']): ?>
                                        <span class="badge text-bg-warning">
                                            Low Stock
                                        </span>
                                    <?php else: ?>
                                        <span class="badge text-bg-success">
                                            OK
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>