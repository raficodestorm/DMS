<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display the contact page for logged-in customer.
     */
    public function index()
    {
        $authUser = auth()->user();

        // Retrieve customer model
        $customer = $authUser->customer;
        if (!$customer && $authUser->customer_id) {
            $customer = Customer::find($authUser->customer_id);
        }

        $branchId = $customer?->branch_id ?? $authUser->branch_id;

        // Assigned SR
        $sr = null;
        if ($customer?->sr_id) {
            $sr = User::with(['employee', 'branch'])->find($customer->sr_id);
        }

        // Branch Manager
        $manager = null;
        if ($branchId) {
            $manager = User::with(['employee', 'branch'])
                ->where('branch_id', $branchId)
                ->where('role', 'manager')
                ->first();
        }

        return view('pages.customer.contact.index', compact('customer', 'sr', 'manager'));
    }
}
