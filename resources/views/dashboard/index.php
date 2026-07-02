<?php

use App\Services\AuthService;

$authService = new AuthService();

$title = 'Dashboard';

?>

<h1>Dashboard</h1>

<p>Welcome to Warehouse ERP System.</p>

<div class="row">
    <?php if ($authService->hasAnyRole(['administrator', 'manager'])): ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Products</h5>
                    <h3>0</h3>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($authService->hasAnyRole(['administrator', 'manager', 'employee'])): ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Warehouses</h5>
                    <h3>0</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Sales</h5>
                    <h3>0</h3>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($authService->hasAnyRole(['administrator', 'manager'])): ?>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Purchases</h5>
                    <h3>0</h3>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>