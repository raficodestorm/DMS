<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DeductionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\BonusController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
  Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
  Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
  Route::get('users/show/{user}', [UserManagementController::class, 'show'])->name('users.show');
  Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
  Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
  Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

  Route::get('/admins', [UserManagementController::class, 'admins'])->name('index.admins');
  Route::get('/manage-managers', [UserManagementController::class, 'managers'])->name('index.managers');
  Route::get('/manage-srs', [UserManagementController::class, 'sr'])->name('index.srs');
  Route::get('/manage-customers', [UserManagementController::class, 'customer'])->name('index.customers');
  Route::get('/normal-users', [UserManagementController::class, 'normalUsers'])->name('index.normalUsers');

  Route::get('/employees/qrcode/{employee}', [EmployeeController::class, 'showqr'])->name('employees.qrcode');
  Route::get('/employees/{employee}/qr-download', [EmployeeController::class, 'downloadQR'])
    ->name('employees.qr.download');

  Route::resource('employees', EmployeeController::class);
  Route::resource('branches', BranchController::class);
  Route::resource('customers', CustomerController::class);
  Route::resource('categories', CategoryController::class);
  Route::resource('costs', CostController::class);
  Route::resource('products', ProductController::class);
  Route::resource('offers', OfferController::class);
  Route::resource('deductions', DeductionController::class);
  Route::resource('suppliers', SupplierController::class);
  Route::resource('bonuses', BonusController::class);


  Route::get('/stock-in-requests/index', [StockRequestController::class, 'stockInRequestIndexForAdmin'])->name('stock.in.requests.index');

  // রিকোয়েস্টের ডিটেইল দেখার জন্য
  Route::get('/stock-in-request/show/{id}', [StockRequestController::class, 'showForAdmin'])->name('stock.in.request.show');

  // এপ্রুভ এবং রিজেক্ট অ্যাকশন
  Route::post('/stock-in-requests/{id}/approve', [StockRequestController::class, 'approve'])->name('stock.in.approve');
  Route::post('/stock-in-requests/{id}/reject', [StockRequestController::class, 'reject'])->name('stock.in.reject');

  // সব ব্রাঞ্চের সামারি ভিউ
  Route::get('/all-stocks', [StockController::class, 'allStocksSummary'])->name('stocks.all');

  // নির্দিষ্ট ব্রাঞ্চ বা টোটাল কোম্পানির ডিটেইল ভিউ
  Route::get('/stock/branch/{branch_id?}', [StockController::class, 'specificStock'])->name('stocks.specific');

  Route::get('/orders', [OrderAdminController::class, 'indexForAllAdmin'])->name('order.index');
  Route::get('/orders/pending', [OrderAdminController::class, 'indexForPendingAdmin'])->name('order.pending.index');
  Route::get('/orders/allsrs', [OrderAdminController::class, 'allSrOrders'])->name('order.all.srs');
  Route::get('/orders/allbranches', [OrderAdminController::class, 'allBranchOrders'])->name('order.all.branches');
  Route::get('/orders/allcustomer', [OrderAdminController::class, 'allCustomerOrders'])->name('order.all.customers');
  Route::get('orders/specific/{id}', [OrderAdminController::class, 'specificSrOrders'])->name('order.specific.sr');
  Route::get('orders/specific/customer/{id}', [OrderAdminController::class, 'specificCustomerOrders'])->name('order.specific.customer');
  Route::get('orders/specific/branch/{id}', [OrderAdminController::class, 'specificBranchOrders'])->name('order.specific.branch');
  // Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
  Route::get('/orders/{order}/show', [OrderController::class, 'showForAdmin'])->name('order.show');
  Route::patch('/orders/approve/{order}', [OrderController::class, 'approve'])->name('order.approve');
  Route::patch('/orders/reject/{order}', [OrderController::class, 'reject'])->name('order.reject');

  Route::get('/order/invoice/view/{order}', [OrderController::class, 'viewInvoice'])->name('order.view_invoice');

  Route::get('/return', [\App\Http\Controllers\Admin\ReturnAdminController::class, 'index'])->name('return.index');
  Route::get('/return/{id}/show', [\App\Http\Controllers\Admin\ReturnAdminController::class, 'show'])->name('return.show');
  // Use POST or PATCH for approval
  Route::post('/return/{id}/approve', [\App\Http\Controllers\Admin\ReturnAdminController::class, 'approve'])->name('return.approve');
  Route::delete('/return/{id}', [\App\Http\Controllers\Admin\ReturnAdminController::class, 'destroy'])->name('return.destroy');


  Route::get('/payments', [PaymentController::class, 'indexForAdmin'])->name('payments.index');
  Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');

  // Admin chaitile shob role-er payment delete o korte parbe
  Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
  Route::get('/payments/show/{payment}', [PaymentController::class, 'showForAdmin'])->name('payments.show');
});
