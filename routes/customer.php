<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Sr\OrderSrController;
use Illuminate\Support\Facades\Route;


Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {

  Route::get('/payments', [PaymentController::class, 'indexForCustomer'])->name('payments.index');
  Route::get('/payments/show/{payment}', [PaymentController::class, 'show'])->name('payments.show');

  Route::get('/orders', [OrderSrController::class, 'indexForCustomer'])->name('orders.index');
  Route::get('/orders/show/{order}', [OrderSrController::class, 'showForCustomer'])->name('orders.show');
});
