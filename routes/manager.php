<?php

use App\Http\Controllers\Manager\EmployeeController;
use App\Http\Controllers\Manager\UserManagementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockRequestController;
use App\Models\StockInRequest;
use Illuminate\Support\Facades\Route;


Route::prefix('manager')->name('manager.')->middleware(['auth', 'role:manager'])->group(function () {
  Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
  Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
  Route::get('users/show/{user}', [UserManagementController::class, 'show'])->name('users.show');
  Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
  Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
  Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

  Route::get('/manage-srs', [UserManagementController::class, 'sr'])->name('index.srs');
  Route::get('/manage-customers', [UserManagementController::class, 'customer'])->name('index.customers');

  Route::resource('employees', EmployeeController::class);



  Route::get('/stock-in-create', [StockRequestController::class, 'createStockRequest'])->name('stock.in.create');
  Route::get('/stock/get-products/{supplier_id}', [StockRequestController::class, 'getProductsBySupplier'])->name('getProducts');
  Route::post('/stock/store', [StockRequestController::class, 'store'])->name('stock.store');
  Route::get('/stock-in-requests/index', [StockRequestController::class, 'stockInRequestIndexForManager'])->name('stock.in.requests.index');

  Route::get('/stock-in-request/{id}', [StockRequestController::class, 'showForManager'])->name('stock.in.request.show');

  Route::delete('/stock-in-request/{id}', [StockRequestController::class, 'stockInDestroy'])->name('stock.in.request.destroy');

  Route::get('/stock-in-request/{id}/edit', [StockRequestController::class, 'stockInEdit'])->name('stock.in.request.edit');

  Route::put('stock-in/request/{id}', [StockRequestController::class, 'stockInUpdate'])->name('stock.in.update');

  Route::get('/stock', [StockController::class, 'managerIndex'])->name('stock.index');

  Route::get('/get-product-data/{id}', [OrderController::class, 'getProductData']);
  Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
  Route::get('/orders/show/{order}', [OrderController::class, 'showForManager'])->name('order.show');
  Route::get('/orders/edit/{order}', [OrderController::class, 'edit'])->name('order.edit');
  Route::put('/orders/update/{order}', [OrderController::class, 'update'])->name('order.update');
  Route::patch('/orders/sendToAdmin/{order}', [OrderController::class, 'sendToAdmin'])->name('order.sendToAdmin');

  Route::patch('/orders/reject/{order}', [OrderController::class, 'reject'])->name('order.reject');
  Route::delete('/orders/destroy/{order}', [OrderController::class, 'destroy'])->name('order.destroy');

  Route::get('/order/invoice/{order}', [OrderController::class, 'confirmAndInvoice'])->name('order.confirm');

  // এটি শুধুমাত্র ইনভয়েস দেখার জন্য
  Route::get('/order/invoice/view/{order}', [OrderController::class, 'viewInvoice'])->name('order.view_invoice');
});
