<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ERP Modules
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('vehicles', \App\Http\Controllers\VehicleController::class);
    Route::post('vehicles/{vehicle}/status', [\App\Http\Controllers\VehicleController::class, 'updateStatus'])->name('vehicles.update-status');
    Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('purchase-orders/{purchaseOrder}/status', [\App\Http\Controllers\PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    Route::post('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    Route::get('/inventory-movements', [\App\Http\Controllers\InventoryMovementController::class, 'index'])->name('inventory-movements.index');
    Route::get('/journal-entries', [\App\Http\Controllers\JournalEntryController::class, 'index'])->name('journal-entries.index');

    // CRM & Leads Module
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::resource('leads', \App\Http\Controllers\LeadController::class);
    Route::post('leads/{lead}/activity', [\App\Http\Controllers\LeadController::class, 'logActivity'])->name('leads.activity');
    Route::get('leads/{lead}/convert', [\App\Http\Controllers\LeadController::class, 'showConvertForm'])->name('leads.convert.form');
    Route::post('leads/{lead}/convert', [\App\Http\Controllers\LeadController::class, 'convertToDeal'])->name('leads.convert');

    // Sales & Deals Module
    Route::resource('deals', \App\Http\Controllers\DealController::class);
    Route::post('deals/{deal}/status', [\App\Http\Controllers\DealController::class, 'updateStatus'])->name('deals.update-status');
    Route::get('deals/{deal}/pdf', [\App\Http\Controllers\DealController::class, 'downloadContract'])->name('deals.pdf');

    // Finance & Payments Module
    Route::get('/finance', [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');
    Route::post('/finance/scan', [\App\Http\Controllers\FinanceController::class, 'scanOverdue'])->name('finance.scan');
    Route::get('/deals/{deal}/invoice', [\App\Http\Controllers\FinanceController::class, 'downloadInvoice'])->name('deals.invoice');
    Route::post('/deals/{deal}/payments', [\App\Http\Controllers\PaymentController::class, 'storePayment'])->name('deals.payments.store');
    Route::post('/deals/{deal}/installments/build', [\App\Http\Controllers\PaymentController::class, 'buildInstallmentPlan'])->name('deals.installments.build');
    Route::post('/installments/{installment}/pay', [\App\Http\Controllers\PaymentController::class, 'payInstallment'])->name('installments.pay');

    // User & Role Management
    Route::resource('users', \App\Http\Controllers\UserController::class);

    // Reports & Dashboard Module
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales/export', [\App\Http\Controllers\ReportController::class, 'exportSales'])->name('reports.sales.export');
});
