<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\Sr\UserManagementController;
use Illuminate\Support\Facades\Route;


Route::prefix('sr')->name('sr.')->middleware(['auth', 'role:sr'])->group(function () {
  Route::get('/customers', [UserManagementController::class, 'customer'])->name('index.customers');
  Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
  Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
  Route::get('users/show/{user}', [UserManagementController::class, 'show'])->name('users.show');
  Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
  Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
  Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

  // Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
  Route::get('/orders/create', [OrderController::class, 'create'])->name('order.create');
  Route::post('/orders/store', [OrderController::class, 'store'])->name('order.store');
  // Route::get('/orders/{order}/show', [OrderController::class, 'show'])->name('order.show');
  // Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('order.edit');
  // Route::post('/orders/{order}/update', [OrderController::class, 'update'])->name('order.update');
  Route::get('/get-product-data/{id}', [OrderController::class, 'getProductData']);
});
