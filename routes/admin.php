<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\OrderController;
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
  Route::resource('suppliers', SupplierController::class);

  Route::get('/stock-in-requests/index', [StockRequestController::class, 'stockInRequestIndexForAdmin'])->name('stock.in.requests.index');

  // রিকোয়েস্টের ডিটেইল দেখার জন্য
  Route::get('/stock-in-request/show/{id}', [StockRequestController::class, 'showForAdmin'])->name('stock.in.request.show');

  // এপ্রুভ এবং রিজেক্ট অ্যাকশন
  Route::post('/stock-in-requests/{id}/approve', [StockRequestController::class, 'approve'])->name('stock.in.approve');
  Route::post('/stock-in-requests/{id}/reject', [StockRequestController::class, 'reject'])->name('stock.in.reject');
});
