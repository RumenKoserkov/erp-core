<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\Category;
use App\Services\AuthService;

class CategoryController extends Controller
{
    private Category $categoryModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->authService = new AuthService();
    }

    public function index(): void
    {
        $currentUser = $this->authService->user();

        $search = '';

        if (isset($_GET['search'])) {
            $search = trim((string)$_GET['search']);
        }

        $categories = $this->categoryModel->allByCompany(
            (int)$currentUser['company_id'],
            $search
        );

        $this->view('categories/index', [
            'title' => 'Categories',
            'categories' => $categories,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->view('categories/create', [
            'title' => 'Create Category',
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
            ->required('name', 'Category name is required.')
            ->max('name', 255, 'Category name must be maximum 255 characters.');

        $errors = $validator->all();

        if ($this->categoryModel->nameExistsInCompany($data['name'], (int)$currentUser['company_id'])) {
            $errors[] = 'Category with this name already exists.';
        }

        if (!empty($errors)) {
            $this->view('categories/create', [
                'title' => 'Create Category',
                'errors' => $errors,
                'old' => $data,
            ]);

            return;
        }

        $data['company_id'] = (int)$currentUser['company_id'];

        $this->categoryModel->create($data);

        Flash::success('Category created successfully.');

        $this->redirect('/categories');
    }

    public function edit(): void
    {
        $currentUser = $this->authService->user();

        $id = 0;

        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
        }

        if ($id <= 0) {
            $this->abort(404);
        }

        $category = $this->categoryModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($category === null) {
            $this->abort(404);
        }

        $this->view('categories/edit', [
            'title' => 'Edit Category',
            'category' => $category,
            'errors' => [],
            'old' => $category,
        ]);
    }

    public function update(): void
    {
        $currentUser = $this->authService->user();

        $id = 0;

        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
        }

        if ($id <= 0) {
            $this->abort(404);
        }

        $category = $this->categoryModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($category === null) {
            $this->abort(404);
        }

        $data = $this->getFormData();

        $validator = new Validator($_POST);

        $validator
            ->required('name', 'Category name is required.')
            ->max('name', 255, 'Category name must be maximum 255 characters.');

        $errors = $validator->all();

        if ($this->categoryModel->nameExistsInCompanyExceptCategory(
            $data['name'],
            (int)$currentUser['company_id'],
            $id
        )) {
            $errors[] = 'Category with this name already exists.';
        }

        if (!empty($errors)) {
            $this->view('categories/edit', [
                'title' => 'Edit Category',
                'category' => $category,
                'errors' => $errors,
                'old' => $data,
            ]);

            return;
        }

        $data['company_id'] = (int)$currentUser['company_id'];

        $this->categoryModel->update($id, $data);

        Flash::success('Category updated successfully.');

        $this->redirect('/categories');
    }

    public function deactivate(): void
    {
        $currentUser = $this->authService->user();

        $id = 0;

        if (isset($_POST['id'])) {
            $id = (int)$_POST['id'];
        }

        if ($id <= 0) {
            $this->abort(404);
        }

        $category = $this->categoryModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($category === null) {
            $this->abort(404);
        }

        $this->categoryModel->deactivate(
            $id,
            (int)$currentUser['company_id']
        );

        Flash::success('Category deactivated successfully.');

        $this->redirect('/categories');
    }

    private function getFormData(): array
    {
        $name = '';
        $description = '';
        $isActive = 0;

        if (isset($_POST['name'])) {
            $name = trim((string)$_POST['name']);
        }

        if (isset($_POST['description'])) {
            $description = trim((string)$_POST['description']);
        }

        if (isset($_POST['is_active'])) {
            $isActive = 1;
        }

        return [
            'name' => $name,
            'description' => $description,
            'is_active' => $isActive,
        ];
    }

    private function emptyOldData(): array
    {
        return [
            'name' => '',
            'description' => '',
            'is_active' => '1',
        ];
    }
}