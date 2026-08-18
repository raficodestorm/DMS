<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Manager\EmployeeController;
use App\Http\Controllers\Manager\OrderManagerController;
use App\Http\Controllers\Manager\RetailOrderController;
use App\Http\Controllers\Manager\UserManagementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockRequestController;
use App\Models\StockInRequest;
use App\Http\Controllers\Manager\BranchCostController;
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
  Route::resource('costs', BranchCostController::class);



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
  Route::get('/orders', [OrderManagerController::class, 'index'])->name('order.index');
  Route::get('/orders/show/{order}', [OrderManagerController::class, 'showForManager'])->name('order.show');
  Route::get('/orders/edit/{order}', [OrderController::class, 'edit'])->name('order.edit');
  Route::put('/orders/update/{order}', [OrderController::class, 'update'])->name('order.update');
  Route::patch('/orders/sendToAdmin/{order}', [OrderManagerController::class, 'sendToAdmin'])->name('order.sendToAdmin');

  Route::patch('/orders/reject/{order}', [OrderController::class, 'reject'])->name('order.reject');
  Route::delete('/orders/destroy/{order}', [OrderManagerController::class, 'destroy'])->name('order.destroy');

  Route::get('/orders/allsrs', [OrderManagerController::class, 'allSrOrders'])->name('order.all.srs');

  Route::get('/return', [\App\Http\Controllers\Manager\ReturnManagerController::class, 'index'])->name('return.index');
  Route::get('/return/create', [\App\Http\Controllers\Manager\ReturnManagerController::class, 'create'])->name('return.create');
  Route::post('/return', [\App\Http\Controllers\Manager\ReturnManagerController::class, 'store'])->name('return.store');
  Route::get('/return/{id}/show', [\App\Http\Controllers\Manager\ReturnManagerController::class, 'show'])->name('return.show');
  Route::post('/return/{id}/forward', [\App\Http\Controllers\Manager\ReturnManagerController::class, 'forwardToAdmin'])->name('return.forward');
  Route::post('/return/{id}/reject', [\App\Http\Controllers\Manager\ReturnManagerController::class, 'reject'])->name('return.reject');
  Route::get('/orders/allcustomer', [OrderManagerController::class, 'allCustomerOrders'])->name('order.all.customers');
  Route::get('order/specific/{id}', [OrderManagerController::class, 'specificSrOrders'])->name('order.specific.sr');
  Route::get('/specific/{id}', [OrderManagerController::class, 'specificCustomerOrders'])->name('order.specific.customer');
  Route::get('/order/invoice/{order}', [OrderManagerController::class, 'confirmAndInvoice'])->name('order.confirm');


  Route::get('/order/invoice/view/{order}', [OrderController::class, 'viewInvoice'])->name('order.view_invoice');
  Route::get('/retail/invoice/view/{order}', [RetailOrderController::class, 'viewRetailInvoice'])->name('order.view_retail_invoice');


  Route::get('/payments', [PaymentController::class, 'indexForManager'])->name('payments.index');
  Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
  Route::post('/payments', [PaymentController::class, 'managerStore'])->name('payments.store');
  Route::get('/payments/show/{payment}', [PaymentController::class, 'show'])->name('payments.show');
  Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
  Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

  // Stock Transfer Routes
  Route::get('/stock-transfer', [\App\Http\Controllers\StockTransferController::class, 'index'])->name('stock-transfer.index');
  Route::get('/stock-transfer/create', [\App\Http\Controllers\StockTransferController::class, 'create'])->name('stock-transfer.create');
  Route::post('/stock-transfer', [\App\Http\Controllers\StockTransferController::class, 'store'])->name('stock-transfer.store');
  Route::get('/stock-transfer/{id}/edit', [\App\Http\Controllers\StockTransferController::class, 'edit'])->name('stock-transfer.edit');
  Route::put('/stock-transfer/{id}', [\App\Http\Controllers\StockTransferController::class, 'update'])->name('stock-transfer.update');
  Route::get('/stock-transfer/{id}', [\App\Http\Controllers\StockTransferController::class, 'show'])->name('stock-transfer.show');
  Route::post('/stock-transfer/{id}/receive', [\App\Http\Controllers\StockTransferController::class, 'receive'])->name('stock-transfer.receive');
  Route::delete('/stock-transfer/{id}', [\App\Http\Controllers\StockTransferController::class, 'destroy'])->name('stock-transfer.destroy');

  // ── Retail Sales (Manager direct orders) ─────────────────────────────
  Route::prefix('retail')->name('retail.')->group(function () {
    Route::get('/',                          [RetailOrderController::class, 'index'])  ->name('index');
    Route::get('/create',                    [RetailOrderController::class, 'create']) ->name('create');
    Route::post('/',                         [RetailOrderController::class, 'store'])  ->name('store');
    Route::get('/{id}',                      [RetailOrderController::class, 'show'])   ->name('show');
    Route::get('/{id}/edit',                 [RetailOrderController::class, 'edit'])   ->name('edit');
    Route::put('/{id}',                      [RetailOrderController::class, 'update']) ->name('update');
    Route::delete('/{id}',                   [RetailOrderController::class, 'destroy'])->name('destroy');
    Route::get('/product-data/{id}',         [RetailOrderController::class, 'getProductData'])->name('product.data');
  });

});
