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

  Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});
