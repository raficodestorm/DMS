<?php

use App\Http\Controllers\Manager\EmployeeController;
use App\Http\Controllers\Manager\UserManagementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockRequestController;
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



  Route::get('/stock-in-create', [StockRequestController::class, 'createStockRequest'])->name('stock.create');
  Route::get('/stock/get-products/{supplier_id}', [StockRequestController::class, 'getProductsBySupplier'])->name('getProducts');
  Route::post('/stock/store', [StockRequestController::class, 'store'])->name('stock.store');
  Route::post('/orders/{id}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
});
