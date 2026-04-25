<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Sr\OrderSrController;
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

  Route::get('/orders', [OrderSrController::class, 'index'])->name('order.index');
  Route::get('/orders/create', [OrderSrController::class, 'create'])->name('order.create');
  Route::post('/orders/store', [OrderSrController::class, 'store'])->name('order.store');
  Route::get('/get-product-data/{id}', [OrderController::class, 'getProductData']);
  Route::get('/orders/edit/{order}', [OrderController::class, 'edit'])->name('order.edit');
  Route::put('/orders/update/{order}', [OrderController::class, 'update'])->name('order.update');
  Route::get('/order/invoice/view/{order}', [OrderController::class, 'viewInvoice'])->name('order.view_invoice');
  Route::get('/orders/show/{order}', [OrderSrController::class, 'showForSr'])->name('order.show');
  Route::patch('/orders/delivered/{order}', [OrderSrController::class, 'delivered'])->name('order.delivered');

  Route::get('/', [OrderSrController::class, 'allOrders'])->name('order.all');
  Route::get('/customer/{id}', [OrderSrController::class, 'customerOrders'])->name('order.specific');



  Route::get('/payments', [PaymentController::class, 'indexForSr'])->name('payments.index');
  Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
  Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

  // Pending payment edit/update/delete korar permission thakbe
  Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
  Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
  Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
  Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});
