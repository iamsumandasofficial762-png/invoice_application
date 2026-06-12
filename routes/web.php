<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::middleware('jwt.web')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::get('/customers/search-json', [CustomerController::class, 'searchJson'])->name('customers.searchJson');
    Route::get('/customers/select2-search', [CustomerController::class, 'select2Search'])->name('customers.select2Search');
    Route::get('/customers/live-search', [CustomerController::class, 'liveSearch'])->name('customers.liveSearch');
    Route::post('/customers/ajax-store', [CustomerController::class, 'ajaxStore'])->name('customers.ajaxStore');
    Route::resource('customers', CustomerController::class);

    Route::get('/invoices/live-search', [InvoiceController::class, 'liveSearch'])->name('invoices.liveSearch');
    Route::get('/invoices/check-number', [InvoiceController::class, 'checkNumber'])->name('invoices.checkNumber');
    Route::resource('invoices', InvoiceController::class);
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::get('/invoices/{invoice}/image', [InvoiceController::class, 'image'])->name('invoices.image');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
});
