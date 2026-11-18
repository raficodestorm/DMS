<?php

use App\Constants\Permissions;
use App\Http\Controllers\Admin\BonusController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CashController;
use App\Http\Controllers\Admin\CashTransferController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyCostController;
use App\Http\Controllers\Admin\CostDashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DeductionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReturnAdminController;
use App\Http\Controllers\Admin\ShippingRateController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Manager\RetailOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockCutController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierTransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,editor'])->group(function () {

    // ==========================================
    // Cash Dashboard & Treasury (manage_cash)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::CASH)->group(function () {
        Route::get('/cash', [CashController::class, 'index'])->name('cash.index');
        Route::get('/cash/closing-history', [CashController::class, 'closingHistory'])->name('cash.closing-history');
        Route::get('/cash/closing-history/data', [CashController::class, 'fetchClosingHistoryData'])->name('cash.closing-history.data');
        Route::get('/cash/ledger', [CashController::class, 'ledgerIndex'])->name('cash.ledger');
        Route::get('/cash/ledger/data', [CashController::class, 'fetchLedgerData'])->name('cash.ledger.data');
        Route::post('/cash/cash-in', [CashController::class, 'cashIn'])->name('cash.cash-in');
        Route::put('/cash/cash-in/{id}', [CashController::class, 'updateCashIn'])->name('cash.cash-in.update');
        Route::post('/cash/cash-out', [CashController::class, 'cashOut'])->name('cash.cash-out');
        Route::put('/cash/cash-out/{id}', [CashController::class, 'updateCashOut'])->name('cash.cash-out.update');
        Route::get('/cash/ledger/{id}', [CashController::class, 'ledgerShow'])->name('cash.ledger.show');
    });

    // ==========================================
    // Cash Transfers (manage_cash_transfers)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::CASH_TRANSFERS)->group(function () {
        Route::get('/cash-transfers', [CashTransferController::class, 'index'])->name('cash-transfers.index');
        Route::get('/cash-transfers/data', [CashTransferController::class, 'fetchData'])->name('cash-transfers.data');
        Route::get('/cash-transfers/{id}', [CashTransferController::class, 'show'])->name('cash-transfers.show');
        Route::post('/cash-transfers/{id}/approve', [CashTransferController::class, 'approve'])->name('cash-transfers.approve');
        Route::post('/cash-transfers/{id}/reject', [CashTransferController::class, 'reject'])->name('cash-transfers.reject');
    });

    // ==========================================
    // Users Management (manage_users)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::USERS)->group(function () {
        Route::get('/users/index', [UserManagementController::class, 'users'])->name('index.users');
        Route::get('/users/index/data', [UserManagementController::class, 'fetchUsersIndexData'])->name('users.index.data');
        Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('users/show/{user}', [UserManagementController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });

    // ==========================================
    // Employees Management (manage_employees)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::EMPLOYEES)->group(function () {
        Route::get('/employees/qrcode/{employee}', [EmployeeController::class, 'showqr'])->name('employees.qrcode');
        Route::get('/employees/{employee}/qr-download', [EmployeeController::class, 'downloadQR'])->name('employees.qr.download');
        Route::get('/employees/index/data', [EmployeeController::class, 'fetchEmployeesIndexData'])->name('employees.index.data');
        Route::resource('employees', EmployeeController::class);
    });

    // ==========================================
    // Customers Management (manage_customers)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::CUSTOMERS)->group(function () {
        Route::get('/customers/ranking', [CustomerController::class, 'ranking'])->name('customers.ranking');
        Route::get('/customers/ranking/data', [CustomerController::class, 'fetchRankingData'])->name('customers.ranking.data');
        Route::get('/customers/{customer}/ranking-position', [CustomerController::class, 'getRankingPositionDetails'])->name('customers.ranking.position');
        Route::get('/customers/index/data', [CustomerController::class, 'fetchCustomersIndexData'])->name('customers.index.data');
        Route::post('/customers/{customer}/change-type', [CustomerController::class, 'changeType'])->name('customers.change-type');
        Route::resource('customers', CustomerController::class);
    });

    // ==========================================
    // Categories Management (manage_categories)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::CATEGORIES)->group(function () {
        Route::get('/categories/index/data', [CategoryController::class, 'fetchCategoriesIndexData'])->name('categories.index.data');
        Route::post('/categories/{category}/toggle-featured', [CategoryController::class, 'toggleFeatured'])->name('categories.toggle-featured');
        Route::resource('categories', CategoryController::class);
    });

    // ==========================================
    // Global Expenses & Costs (manage_costs)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::COSTS)->group(function () {
        Route::get('/company_costs/index/data', [CompanyCostController::class, 'fetchCompanyCostsIndexData'])->name('company_costs.index.data');
        Route::get('/costs-dashboard', [CostDashboardController::class, 'index'])->name('costs.dashboard');
        Route::get('/costs-branch/{id}', [CostDashboardController::class, 'branchCosts'])->name('costs.branch');
        Route::resource('company_costs', CompanyCostController::class);
    });

    // ==========================================
    // Products & Barcode Print (manage_products)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::PRODUCTS)->group(function () {
        Route::get('/products/barcode-print', [ProductController::class, 'barcodePrint'])->name('products.barcode');
        Route::get('/products/index/data', [ProductController::class, 'fetchProductsIndexData'])->name('products.index.data');
        Route::get('/products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
        Route::post('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
        Route::resource('products', ProductController::class);
    });

    // ==========================================
    // Stock & Transfers (manage_stock)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::STOCK)->group(function () {
        Route::get('/stock-in-requests/index', [StockRequestController::class, 'stockInRequestIndexForAdmin'])->name('stock.in.requests.index');
        Route::get('/stock-in-requests/data', [StockRequestController::class, 'fetchStockInRequestsData'])->name('stock.in.requests.data');
        Route::get('/stock-in-request/show/{id}', [StockRequestController::class, 'showForAdmin'])->name('stock.in.request.show');
        Route::get('/stock-in-request/invoice/{id}', [StockRequestController::class, 'viewInvoice'])->name('stock.in.request.invoice');
        Route::post('/stock-in-requests/{id}/approve', [StockRequestController::class, 'approve'])->name('stock.in.approve');
        Route::post('/stock-in-requests/{id}/reject', [StockRequestController::class, 'reject'])->name('stock.in.reject');
        Route::get('/stock/get-products/{supplier_id}', [StockRequestController::class, 'getProductsBySupplier'])->name('getProducts');
        Route::get('/stock-in-request/{id}/edit', [StockRequestController::class, 'stockInAdminEdit'])->name('stock.in.request.edit');
        Route::put('stock-in/request/{id}', [StockRequestController::class, 'stockInAdminUpdate'])->name('stock.in.update');
        Route::get('/all-stocks', [StockController::class, 'allStocksSummary'])->name('stocks.all');
        Route::get('/stock/branch/{branch_id?}', [StockController::class, 'specificStock'])->name('stocks.specific');

        // Stock Transfer
        Route::get('/stock-transfer', [StockTransferController::class, 'indexForAdmin'])->name('stock-transfer.index');
        Route::get('/stock-transfer/index/data', [StockTransferController::class, 'fetchTransfersIndexData'])->name('stock-transfer.index.data');
        Route::get('/stock-transfer/{id}', [StockTransferController::class, 'show'])->name('stock-transfer.show');
        Route::post('/stock-transfer/{id}/approve', [StockTransferController::class, 'approve'])->name('stock-transfer.approve');
        Route::post('/stock-transfer/{id}/reject', [StockTransferController::class, 'reject'])->name('stock-transfer.reject');
        Route::delete('/stock-transfer/{id}', [StockTransferController::class, 'destroy'])->name('stock-transfer.destroy');
    });

    // ==========================================
    // Orders Management (manage_orders)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::ORDERS)->group(function () {
        Route::get('/orders', [OrderAdminController::class, 'indexForAllAdmin'])->name('order.index');
        Route::get('/orders/data', [OrderAdminController::class, 'fetchOrdersData'])->name('order.data');
        Route::get('/orders/allsrs', [OrderAdminController::class, 'allSrOrders'])->name('order.all.srs');
        Route::get('/orders/allbranches', [OrderAdminController::class, 'allBranchOrders'])->name('order.all.branches');
        Route::get('/orders/allcustomer', [OrderAdminController::class, 'allCustomerOrders'])->name('order.all.customers');
        Route::get('orders/specific/{id}', [OrderAdminController::class, 'specificSrOrders'])->name('order.specific.sr');
        Route::get('orders/specific/customer/{id}', [OrderAdminController::class, 'specificCustomerOrders'])->name('order.specific.customer');
        Route::get('orders/specific/branch/{id}', [OrderAdminController::class, 'specificBranchOrders'])->name('order.specific.branch');
        Route::get('/orders/{order}/show', [OrderController::class, 'showForAdmin'])->name('order.show');
        Route::patch('/orders/approve/{order}', [OrderController::class, 'approve'])->name('order.approve');
        Route::patch('/orders/online-approve/{order}', [OrderAdminController::class, 'onlineApprove'])->name('order.online_approve');
        Route::patch('/orders/reject/{order}', [OrderController::class, 'reject'])->name('order.reject');
        Route::patch('/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('order.update_status');
        Route::get('/order/invoice/view/{order}', [OrderController::class, 'viewInvoice'])->name('order.view_invoice');
        Route::get('/retail/invoice/view/{order}', [RetailOrderController::class, 'viewRetailInvoice'])->name('order.view_retail_invoice');
        Route::get('/online/invoice/view/{order}', [OrderController::class, 'viewOnlineInvoice'])->name('order.view_online_invoice');
    });

    // ==========================================
    // Order Returns (manage_order_returns)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::ORDER_RETURN)->group(function () {
        Route::get('/return', [ReturnAdminController::class, 'index'])->name('return.index');
        Route::get('/return/index/data', [ReturnAdminController::class, 'fetchReturnsIndexData'])->name('return.index.data');
        Route::get('/return/{id}/show', [ReturnAdminController::class, 'show'])->name('return.show');
        Route::post('/return/{id}/approve', [ReturnAdminController::class, 'approve'])->name('return.approve');
        Route::delete('/return/{id}', [ReturnAdminController::class, 'destroy'])->name('return.destroy');
    });

    // ==========================================
    // Customer Ledger / Payments (manage_customer_ledger)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::CUSTOMER_LEDGER)->group(function () {
        Route::get('/payments', [PaymentController::class, 'indexForAdmin'])->name('payments.index');
        Route::get('/payments/index/data', [PaymentController::class, 'fetchPaymentsIndexData'])->name('payments.index.data');
        Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('/payments/show/{payment}', [PaymentController::class, 'showForAdmin'])->name('payments.show');
    });

    // ==========================================
    // Reports & Analytics (view_reports)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::REPORTS)->group(function () {
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/sales', [ReportController::class, 'sales'])->name('report.sales');
        Route::get('/report/stock-in', [ReportController::class, 'stockIn'])->name('report.stock-in');
        Route::get('/report/inventory', [ReportController::class, 'inventory'])->name('report.inventory');
        Route::get('/report/financial', [ReportController::class, 'financial'])->name('report.financial');
        Route::get('/report/branch-performance', [ReportController::class, 'branchPerformance'])->name('report.branch-performance');
        Route::get('/report/sr-performance', [ReportController::class, 'srPerformance'])->name('report.sr-performance');
        Route::get('/report/sr-performance/{srId}', [ReportController::class, 'srPerformanceDetail'])->name('report.sr-performance.detail');
        Route::get('/report/dso-performance', [ReportController::class, 'dsoPerformance'])->name('report.dso-performance');
        Route::get('/report/dso-performance/{dsoId}', [ReportController::class, 'dsoPerformanceDetail'])->name('report.dso-performance.detail');
    });

    // ==========================================
    // Stock Returns / Stock Cuts (manage_stock_returns)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::STOCK_RETURN)->group(function () {
        Route::get('/stock-cut-create', [StockCutController::class, 'createStockCut'])->name('stock.cut.create');
        Route::get('/stock/get-products/{supplier_id}', [StockRequestController::class, 'getProductsBySupplier'])->name('getProducts');
        Route::get('/stock/get-products/{supplier_id}/{branch_id}', [StockCutController::class, 'getProductsBySupplierAndBranch'])->name('getProductsByBranch');
        Route::post('/stock/store', [StockCutController::class, 'store'])->name('stockcut.store');
        Route::get('/stock-cut-cuts/index', [StockCutController::class, 'index'])->name('stock.cut.cuts.index');
        Route::get('/stock-cut/index/data', [StockCutController::class, 'fetchStockCutsIndexData'])->name('stock.cut.index.data');
        Route::get('/stock-cut-cut/{id}', [StockCutController::class, 'show'])->name('stock.cut.cut.show');
        Route::delete('/stock-cut-cut/{id}', [StockCutController::class, 'destroy'])->name('stock.cut.cut.destroy');
        Route::get('/stock-cut-cut/{id}/edit', [StockCutController::class, 'edit'])->name('stock.cut.cut.edit');
        Route::put('stock-cut/cut/{id}', [StockCutController::class, 'update'])->name('stock.cut.update');
        Route::post('/stock-cut-cut/{id}/approve', [StockCutController::class, 'approve'])->name('stock.cut.approve');
        Route::post('/stock-cut-cut/{id}/reject', [StockCutController::class, 'reject'])->name('stock.cut.reject');
    });

    // ==========================================
    // Suppliers Management (manage_suppliers)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::SUPPLIERS)->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    // ==========================================
    // Supplier Ledger & Transactions (manage_supplier_ledger)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::SUPPLIER_LEDGER)->group(function () {
        Route::get('/supplier-transactions', [SupplierTransactionController::class, 'indexForAdmin'])->name('supplier-transactions.index');
        Route::get('/supplier-transactions/create', [SupplierTransactionController::class, 'createForAdmin'])->name('supplier-transactions.create');
        Route::post('/supplier-transactions', [SupplierTransactionController::class, 'storeForAdmin'])->name('supplier-transactions.store');
        Route::get('/supplier-transactions/data', [SupplierTransactionController::class, 'fetchSupplierTransactionsIndexData'])->name('supplier-transactions.index.data');
        Route::get('/supplier-transactions/{id}', [SupplierTransactionController::class, 'showForAdmin'])->name('supplier-transactions.show');
        Route::get('/supplier-transactions/{id}/edit', [SupplierTransactionController::class, 'editForAdmin'])->name('supplier-transactions.edit');
        Route::put('/supplier-transactions/{id}', [SupplierTransactionController::class, 'updateForAdmin'])->name('supplier-transactions.update');
        Route::delete('/supplier-transactions/{id}', [SupplierTransactionController::class, 'destroyForAdmin'])->name('supplier-transactions.destroy');
    });

    // ==========================================
    // Offers Management (manage_offers)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::OFFERS)->group(function () {
        Route::get('/offers/index/data', [OfferController::class, 'fetchOffersIndexData'])->name('offers.index.data');
        Route::resource('offers', OfferController::class);
    });

    // ==========================================
    // Bonus & Deductions (manage_bonuses)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::BONUSES)->group(function () {
        Route::get('/bonuses/index/data', [BonusController::class, 'fetchBonusesIndexData'])->name('bonuses.index.data');
        Route::resource('bonuses', BonusController::class);
        Route::resource('deductions', DeductionController::class);
    });

    // ==========================================
    // Shipping Rates (manage_shipping_rates)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::SHIPPING_RATES)->group(function () {
        Route::get('/shipping-rates/index/data', [ShippingRateController::class, 'fetchShippingRatesIndexData'])->name('shipping-rates.index.data');
        Route::match(['post', 'patch'], '/shipping-rates/{shippingRate}/toggle-status', [ShippingRateController::class, 'toggleStatus'])->name('shipping-rates.toggle-status');
        Route::resource('shipping-rates', ShippingRateController::class);
    });

    // ==========================================
    // Slider Control (manage_slides)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::SLIDES)->group(function () {
        Route::get('/slides/index/data', [SlideController::class, 'fetchSlidesIndexData'])->name('slides.index.data');
        Route::match(['post', 'patch'], '/slides/{slide}/toggle-status', [SlideController::class, 'toggleStatus'])->name('slides.toggle-status');
        Route::resource('slides', SlideController::class);
    });

    // ==========================================
    // Branches Management (manage_branches)
    // ==========================================
    Route::middleware('editor.permission:' . Permissions::BRANCHES)->group(function () {
        Route::resource('branches', BranchController::class);
    });

});
