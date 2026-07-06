<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\AuthService;

class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    private Supplier $supplierModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->supplierModel = new Supplier();
        $this->authService = new AuthService();
    }

    public function index(): void
    {
        $currentUser = $this->authService->user();

        $search = '';

        if (isset($_GET['search'])) {
            $search = trim((string)$_GET['search']);
        }

        $products = $this->productModel->allByCompany(
            (int)$currentUser['company_id'],
            $search
        );

        $this->view('products/index', [
            'title' => 'Products',
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $currentUser = $this->authService->user();

        $this->view('products/create', [
            'title' => 'Create Product',
            'categories' => $this->categoryModel->activeByCompany(
                (int)$currentUser['company_id']
            ),
            'suppliers' => $this->supplierModel->activeByCompany(
                (int)$currentUser['company_id']
            ),
            'errors' => [],
            'old' => $this->emptyOldData(),
            'units' => $this->units(),
        ]);
    }

    public function store(): void
    {
        $currentUser = $this->authService->user();

        $data = $this->getFormData();

        $validator = new Validator($_POST);

        $validator
            ->required('name', 'Product name is required.')
            ->max('name', 255, 'Product name must be maximum 255 characters.')
            ->required('category_id', 'Category is required.')
            ->required('unit', 'Unit is required.')
            ->numeric('purchase_price', 'Purchase price must be numeric.')
            ->numeric('selling_price', 'Selling price must be numeric.')
            ->numeric('min_stock', 'Minimum stock must be numeric.');

        $errors = $validator->all();

        if (!in_array($data['unit'], $this->units(), true)) {
            $errors[] = 'Invalid product unit.';
        }

        if ($data['category_id'] <= 0) {
            $errors[] = 'Please select a valid category.';
        }

        if ($data['purchase_price'] < 0) {
            $errors[] = 'Purchase price cannot be negative.';
        }

        if ($data['selling_price'] < 0) {
            $errors[] = 'Selling price cannot be negative.';
        }

        if ($data['min_stock'] < 0) {
            $errors[] = 'Minimum stock cannot be negative.';
        }

        if (
            $this->productModel->barcodeExistsInCompany(
                $data['barcode'],
                (int)$currentUser['company_id']
            )
        ) {
            $errors[] = 'Product with this barcode already exists.';
        }

        if (!empty($errors)) {
            $this->view('products/create', [
                'title' => 'Create Product',
                'categories' => $this->categoryModel->activeByCompany(
                    (int)$currentUser['company_id']
                ),
                'suppliers' => $this->supplierModel->activeByCompany(
                    (int)$currentUser['company_id']
                ),
                'errors' => $errors,
                'old' => $data,
                'units' => $this->units(),
            ]);

            return;
        }

        $data['company_id'] = (int)$currentUser['company_id'];
        $data['internal_code'] = $this->productModel->generateNextInternalCode(
            (int)$currentUser['company_id']
        );
        $data['image_path'] = null;

        $this->productModel->create($data);

        Flash::success('Product created successfully.');

        $this->redirect('/products');
    }

    private function getFormData(): array
    {
        $supplierId = null;

        if (isset($_POST['supplier_id']) && (int)$_POST['supplier_id'] > 0) {
            $supplierId = (int)$_POST['supplier_id'];
        }

        return [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'supplier_id' => $supplierId,
            'barcode' => trim((string)($_POST['barcode'] ?? '')),
            'name' => trim((string)($_POST['name'] ?? '')),
            'unit' => trim((string)($_POST['unit'] ?? '')),
            'purchase_price' => (float)($_POST['purchase_price'] ?? 0),
            'selling_price' => (float)($_POST['selling_price'] ?? 0),
            'min_stock' => (float)($_POST['min_stock'] ?? 0),
            'description' => trim((string)($_POST['description'] ?? '')),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function emptyOldData(): array
    {
        return [
            'category_id' => '',
            'supplier_id' => '',
            'barcode' => '',
            'name' => '',
            'unit' => 'piece',
            'purchase_price' => '0.00',
            'selling_price' => '0.00',
            'min_stock' => '0',
            'description' => '',
            'is_active' => '1',
        ];
    }

    private function units(): array
    {
        return [
            'piece',
            'kg',
            'liter',
            'meter',
        ];
    }
}