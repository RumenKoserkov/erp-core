<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\Supplier;
use App\Services\AuthService;

class SupplierController extends Controller
{
    private Supplier $supplierModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->supplierModel = new Supplier();
        $this->authService = new AuthService();
    }

    public function index(): void
    {
        $currentUser = $this->authService->user();

        $search = trim((string)($_GET['search'] ?? ''));

        $suppliers = $this->supplierModel->allByCompany(
            (int)$currentUser['company_id'],
            $search
        );

        $this->view('suppliers/index', [
            'title' => 'Suppliers',
            'suppliers' => $suppliers,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->view('suppliers/create', [
            'title' => 'Create Supplier',
            'errors' => [],
            'old' => $this->emptyOldData(),
        ]);
    }

    public function store(): void
    {
        $currentUser = $this->authService->user();

        $data = $this->getFormData();

        $validator = new Validator($_POST);

        $validator
            ->required('name', 'Supplier name is required.')
            ->max('name', 255, 'Supplier name must be maximum 255 characters.')
            ->email('email', 'Email must be a valid email address.');

        $errors = $validator->all();

        if (!empty($errors)) {
            $this->view('suppliers/create', [
                'title' => 'Create Supplier',
                'errors' => $errors,
                'old' => $data,
            ]);

            return;
        }

        $data['company_id'] = (int)$currentUser['company_id'];

        $this->supplierModel->create($data);

        Flash::success('Supplier created successfully.');

        $this->redirect('/suppliers');
    }

    public function edit(): void
    {
        $currentUser = $this->authService->user();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->abort(404);
        }

        $supplier = $this->supplierModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($supplier === null) {
            $this->abort(404);
        }

        $this->view('suppliers/edit', [
            'title' => 'Edit Supplier',
            'supplier' => $supplier,
            'errors' => [],
            'old' => $supplier,
        ]);
    }

    public function update(): void
    {
        $currentUser = $this->authService->user();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->abort(404);
        }

        $supplier = $this->supplierModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($supplier === null) {
            $this->abort(404);
        }

        $data = $this->getFormData();

        $validator = new Validator($_POST);

        $validator
            ->required('name', 'Supplier name is required.')
            ->max('name', 255, 'Supplier name must be maximum 255 characters.')
            ->email('email', 'Email must be a valid email address.');

        $errors = $validator->all();

        if (!empty($errors)) {
            $this->view('suppliers/edit', [
                'title' => 'Edit Supplier',
                'supplier' => $supplier,
                'errors' => $errors,
                'old' => $data,
            ]);

            return;
        }

        $data['company_id'] = (int)$currentUser['company_id'];

        $this->supplierModel->update($id, $data);

        Flash::success('Supplier updated successfully.');

        $this->redirect('/suppliers');
    }

    public function deactivate(): void
    {
        $currentUser = $this->authService->user();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->abort(404);
        }

        $supplier = $this->supplierModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($supplier === null) {
            $this->abort(404);
        }

        $this->supplierModel->deactivate($id, (int)$currentUser['company_id']);

        Flash::success('Supplier deactivated successfully.');

        $this->redirect('/suppliers');
    }

    private function getFormData(): array
    {
        return [
            'name' => trim((string)($_POST['name'] ?? '')),
            'phone' => trim((string)($_POST['phone'] ?? '')),
            'email' => trim((string)($_POST['email'] ?? '')),
            'address' => trim((string)($_POST['address'] ?? '')),
            'company_name' => trim((string)($_POST['company_name'] ?? '')),
            'eik' => trim((string)($_POST['eik'] ?? '')),
            'contact_person' => trim((string)($_POST['contact_person'] ?? '')),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function emptyOldData(): array
    {
        return [
            'name' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
            'company_name' => '',
            'eik' => '',
            'contact_person' => '',
            'is_active' => '1',
        ];
    }
}