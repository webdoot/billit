<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customer Routes
    Route::get('/customers/export', [\App\Http\Controllers\CustomerController::class, 'export'])->name('customers.export');
    Route::get('/customers/{customer}/ledger', [\App\Http\Controllers\CustomerController::class, 'ledger'])->name('customers.ledger');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);

    // Service Catalogue Routes
    Route::resource('service-categories', \App\Http\Controllers\ServiceCategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('service-products', \App\Http\Controllers\ServiceProductController::class);

    // Customer Services Routes
    Route::get('/customer-services/{customer_service}/renew', [\App\Http\Controllers\CustomerServiceController::class, 'showRenewForm'])->name('customer-services.renew');
    Route::post('/customer-services/{customer_service}/renew', [\App\Http\Controllers\CustomerServiceController::class, 'renew'])->name('customer-services.renew.post');
    Route::post('/customer-services/{customer_service}/generate-invoice', [\App\Http\Controllers\CustomerServiceController::class, 'generateInvoice'])->name('customer-services.generate-invoice');
    Route::resource('customer-services', \App\Http\Controllers\CustomerServiceController::class);

    // Invoices Routes
    Route::get('/invoices/customer/{customer}/services', [\App\Http\Controllers\InvoiceController::class, 'getCustomerServices'])->name('invoices.services');
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\InvoiceController::class, 'exportPdf'])->name('invoices.pdf');
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);

    // Payments & Receipts Routes
    Route::resource('payments', \App\Http\Controllers\PaymentController::class)->only(['index', 'store', 'destroy']);
    Route::get('/receipts', [\App\Http\Controllers\ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{receipt}', [\App\Http\Controllers\ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/pdf', [\App\Http\Controllers\ReceiptController::class, 'exportPdf'])->name('receipts.pdf');

    // Infrastructure Routes
    Route::resource('servers', \App\Http\Controllers\ServerController::class);
    Route::resource('hostings', \App\Http\Controllers\HostingController::class);
    Route::resource('domains', \App\Http\Controllers\DomainController::class);

    // Reports Routes
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    // Help & Support Routes
    Route::get('/help', [\App\Http\Controllers\HelpController::class, 'index'])->name('help.index');
});
