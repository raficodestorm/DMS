<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\Dso\DeliveryController;
use App\Http\Controllers\Dso\OrderDsoController;
use App\Http\Controllers\Manager\RetailOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('dso')->name('dso.')->middleware(['auth', 'role:dso'])->group(function () {
  Route::get('/order/invoice/view/{order}', [OrderController::class, 'viewInvoice'])->name('order.view_invoice');
  Route::get('/order/online-invoice/view/{order}', [OrderController::class, 'viewOnlineInvoice'])->name('order.view_online_invoice');
  Route::get('/retail/invoice/view/{order}', [RetailOrderController::class, 'viewRetailInvoice'])->name('order.view_retail_invoice');

  Route::get('/orders/show/{order}', [OrderDsoController::class, 'show'])->name('order.show');
  Route::get('/orders/delivered-list', [OrderDsoController::class, 'deliveredOrdersIndex'])->name('order.delivered_list');
  Route::get('/orders/delivered-list/data', [OrderDsoController::class, 'fetchDeliveredOrdersData'])->name('order.delivered_list.data');
  Route::patch('/orders/delivered/{order}', [DeliveryController::class, 'delivered'])->name('order.delivered');
  Route::get('/orders/delivery', [DeliveryController::class, 'deliveryIndex'])->name('order.delivery.index');
  Route::get('/orders/delivery/search', [DeliveryController::class, 'deliverySearch'])->name('order.delivery.search');
});
