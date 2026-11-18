<?php

namespace App\Constants;

class Permissions
{
    public const DASHBOARD = 'view_dashboard';
    public const ORDERS = 'manage_orders';
    public const PRODUCTS = 'manage_products';
    public const STOCK = 'manage_stock';
    public const CUSTOMERS = 'manage_customers';
    public const CUSTOMER_LEDGER = 'manage_customer_ledger';
    public const SUPPLIERS = 'manage_suppliers';
    public const SUPPLIER_LEDGER = 'manage_supplier_ledger';
    public const CASH = 'manage_cash';
    public const CASH_TRANSFERS = 'manage_cash_transfers';
    public const ORDER_RETURN = 'manage_order_returns';
    public const STOCK_RETURN = 'manage_stock_returns';
    public const EMPLOYEES = 'manage_employees';
    public const BRANCHES = 'manage_branches';
    public const CATEGORIES = 'manage_categories';
    public const OFFERS = 'manage_offers';
    public const SLIDES = 'manage_slides';
    public const BONUSES = 'manage_bonuses';
    public const COSTS = 'manage_costs';
    public const SHIPPING_RATES = 'manage_shipping_rates';
    public const REPORTS = 'view_reports';
    public const USERS = 'manage_users';

    /**
     * Grouped permissions with labels for UI presentation.
     */
    public static function grouped(): array
    {
        return [
            'Core' => [
                self::DASHBOARD => [
                    'label' => 'Dashboard Overview',
                    'desc'  => 'Admin dashboard stats, sales charts & monthly metrics',
                ],
                self::REPORTS => [
                    'label' => 'Reports & Analytics',
                    'desc'  => 'Sales, inventory, stock-in, financial & performance reports',
                ],
            ],
            'Sales & Orders' => [
                self::ORDERS => [
                    'label' => 'Orders Management',
                    'desc'  => 'All orders, approve, reject, status change & invoices',
                ],
                self::ORDER_RETURN => [
                    'label' => 'Order Returns',
                    'desc'  => 'Return requests, details, approval & delete',
                ],
                self::CUSTOMERS => [
                    'label' => 'Customers',
                    'desc'  => 'Customer list, ranking, details & type toggle',
                ],
                self::CUSTOMER_LEDGER => [
                    'label' => 'Customer Ledger (Payments)',
                    'desc'  => 'Payments view, approval & payment slip',
                ],
            ],
            'Inventory & Products' => [
                self::PRODUCTS => [
                    'label' => 'Products & Barcode',
                    'desc'  => 'Product catalog, add/edit, export & barcode printing',
                ],
                self::CATEGORIES => [
                    'label' => 'Categories',
                    'desc'  => 'Category list, add, edit & featured toggle',
                ],
                self::STOCK => [
                    'label' => 'Stock & Transfers',
                    'desc'  => 'Stock summary, stock-in history, approval & branch transfers',
                ],
                self::STOCK_RETURN => [
                    'label' => 'Stock Return (Stock Cut)',
                    'desc'  => 'Stock cuts list, create, edit, approve & reject',
                ],
            ],
            'Suppliers & Purchasing' => [
                self::SUPPLIERS => [
                    'label' => 'Suppliers',
                    'desc'  => 'Supplier list, profile & contact information',
                ],
                self::SUPPLIER_LEDGER => [
                    'label' => 'Supplier Ledger & Transactions',
                    'desc'  => 'Supplier transactions, payments, ledger & payment slips',
                ],
            ],
            'Finance & Cash' => [
                self::CASH => [
                    'label' => 'Cash Treasury & Ledger',
                    'desc'  => 'Admin cash-in, cash-out, closing history & cash ledger',
                ],
                self::CASH_TRANSFERS => [
                    'label' => 'Cash Transfers',
                    'desc'  => 'Branch to admin cash transfer review, approve & reject',
                ],
                self::COSTS => [
                    'label' => 'Global Expenses / Costs',
                    'desc'  => 'Cost dashboard, record global cost & expense history',
                ],
            ],
            'HR & Operations' => [
                self::EMPLOYEES => [
                    'label' => 'Employees',
                    'desc'  => 'Employee list, add, edit, QR code & download',
                ],
                self::BRANCHES => [
                    'label' => 'Branches',
                    'desc'  => 'Branch management & branch profiles',
                ],
                self::BONUSES => [
                    'label' => 'Bonus & Deductions',
                    'desc'  => 'Employee bonus management & deduction history',
                ],
                self::USERS => [
                    'label' => 'User Accounts',
                    'desc'  => 'User list, create & edit accounts',
                ],
            ],
            'Marketing & Settings' => [
                self::OFFERS => [
                    'label' => 'Offers & Campaigns',
                    'desc'  => 'Create and manage promotional offers',
                ],
                self::SLIDES => [
                    'label' => 'Slider Controls',
                    'desc'  => 'Homepage banner slides & status control',
                ],
                self::SHIPPING_RATES => [
                    'label' => 'Shipping Rates',
                    'desc'  => 'Courier and district delivery charge rates',
                ],
            ],
        ];
    }

    /**
     * Flat list of all valid permission keys.
     */
    public static function all(): array
    {
        $keys = [];
        foreach (self::grouped() as $group => $items) {
            foreach ($items as $key => $meta) {
                $keys[] = $key;
            }
        }
        return $keys;
    }
}
