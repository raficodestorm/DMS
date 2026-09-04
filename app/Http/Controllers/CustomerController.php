<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.common.customer.index');
    }

    /**
     * Fetch customers index data via AJAX for common customer index page.
     */
    public function fetchCustomersIndexData(Request $request)
    {
        $query = Customer::where('branch_id', auth()->user()->branch_id)->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            if (str_starts_with(strtoupper($search), 'BRC200')) {
                $id = str_replace(['BRC200', 'brc200'], '', $search);
                $query->where('id', $id);
            } elseif (str_starts_with(strtoupper($search), 'BRC')) {
                $id = str_replace(['BRC', 'brc'], '', $search);
                $query->where('id', $id);
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('shop_name', 'LIKE', "%{$search}%")
                      ->orWhere('manager', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('id', 'LIKE', "%{$search}%");
                });
            }
        }

        $totalCount = $query->count();
        $customers = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.common.customer.table', compact('customers'))->render(),
            'mobile'     => view('pages.common.customer.mtable', compact('customers'))->render(),
            'total'      => $totalCount,
            'pagination' => $customers->links()->toHtml(),
        ]);
    }

    public function create()
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('pages.common.customer.create', compact('branches'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'shop_name' => 'required|string|max:150',
            'manager' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:255',
        ]);
        $validated['branch_id'] = auth()->user()->branch_id;
        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer added successfully!');
    }

    public function show(Customer $customer)
    {
        return view('pages.common.customer.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('pages.common.customer.edit', compact('customer', 'branches'));
    }

    public function update(Request $request, Customer $customer)
    {

        $validated = $request->validate([
            'shop_name' => 'required|string|max:150',
            'manager' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:255',
            // 'branch_id' => 'nullable|integer|max:100',
        ]);
        $validated['branch_id'] = auth()->user()->branch_id;
        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        // check if customer has user account
        if ($customer->users()->exists()) {
            return back()->with('error', 'This customer has user account. Please delete the user account first.');
        }

        $customer->delete();

        return redirect()->route('dashboards')
            ->with('success', 'Customer deleted successfully');
    }
}
