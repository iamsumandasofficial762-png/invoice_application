<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/customers/live-search', [CustomerController::class, 'liveSearch'])->name('customers.liveSearch');
Route::post('/customers/ajax-store', [CustomerController::class, 'ajaxStore'])->name('customers.ajaxStore');
Route::resource('customers', CustomerController::class);

Route::get('/invoices/live-search', [InvoiceController::class, 'liveSearch'])->name('invoices.liveSearch');
Route::resource('invoices', InvoiceController::class);
Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
