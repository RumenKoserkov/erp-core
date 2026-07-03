<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Validator;
use App\Models\Client;
use App\Services\AuthService;

class ClientController extends Controller
{
    private Client $clientModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->clientModel = new Client();
        $this->authService = new AuthService();
    }

    public function index(): void
    {
        $currentUser = $this->authService->user();

        $search = trim((string)($_GET['search'] ?? ''));

        $clients = $this->clientModel->allByCompany(
            (int)$currentUser['company_id'],
            $search
        );

        $this->view('clients/index', [
            'title' => 'Clients',
            'clients' => $clients,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->view('clients/create', [
            'title' => 'Create Client',
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
            ->required('name', 'Client name is required.')
            ->max('name', 255, 'Client name must be maximum 255 characters.')
            ->email('email', 'Email must be a valid email address.');

        $errors = $validator->all();

        if (!empty($errors)) {
            $this->view('clients/create', [
                'title' => 'Create Client',
                'errors' => $errors,
                'old' => $data,
            ]);

            return;
        }

        $data['company_id'] = (int)$currentUser['company_id'];

        $this->clientModel->create($data);

        Flash::success('Client created successfully.');

        $this->redirect('/clients');
    }

    public function edit(): void
    {
        $currentUser = $this->authService->user();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->abort(404);
        }

        $client = $this->clientModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($client === null) {
            $this->abort(404);
        }

        $this->view('clients/edit', [
            'title' => 'Edit Client',
            'client' => $client,
            'errors' => [],
            'old' => $client,
        ]);
    }

    public function update(): void
    {
        $currentUser = $this->authService->user();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->abort(404);
        }

        $client = $this->clientModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($client === null) {
            $this->abort(404);
        }

        $data = $this->getFormData();

        $validator = new Validator($_POST);

        $validator
            ->required('name', 'Client name is required.')
            ->max('name', 255, 'Client name must be maximum 255 characters.')
            ->email('email', 'Email must be a valid email address.');

        $errors = $validator->all();

        if (!empty($errors)) {
            $this->view('clients/edit', [
                'title' => 'Edit Client',
                'client' => $client,
                'errors' => $errors,
                'old' => $data,
            ]);

            return;
        }

        $data['company_id'] = (int)$currentUser['company_id'];

        $this->clientModel->update($id, $data);

        Flash::success('Client updated successfully.');

        $this->redirect('/clients');
    }

    public function deactivate(): void
    {
        $currentUser = $this->authService->user();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->abort(404);
        }

        $client = $this->clientModel->findByIdAndCompany(
            $id,
            (int)$currentUser['company_id']
        );

        if ($client === null) {
            $this->abort(404);
        }

        $this->clientModel->deactivate($id, (int)$currentUser['company_id']);

        Flash::success('Client deactivated successfully.');

        $this->redirect('/clients');
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
