<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCash;
use App\Models\CompanyCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyCostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('pages.admin.cost.index');
    }

    public function fetchCompanyCostsIndexData(Request $request)
    {
        $query = CompanyCost::with('creator')->latest('cost_date');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('month')) {
            $query->whereMonth('cost_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('cost_date', $request->year);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('cost_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('cost_date', '<=', $request->to_date);
        }

        $totalCost = (clone $query)->sum('amount');
        $costs = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.admin.cost.table', compact('costs'))->render(),
            'mobile'     => view('pages.admin.cost.mtable', compact('costs'))->render(),
            'pagination' => (string) $costs->links(),
            'total'      => $costs->total(),
            'totalCost'  => number_format($totalCost, 2) . ' ৳',
        ]);
    }

    /**
     * Get or create the AdminCash record.
     * Locks for update to prevent concurrent race conditions.
     */
    protected function getAdminCashRecord(): AdminCash
    {
        $adminId = Auth::id();

        // 1. Try to find the record belonging to the authenticated admin
        $adminCash = AdminCash::where('admin_id', $adminId)->lockForUpdate()->first();

        // 2. Fallback: any existing admin cash record
        if (!$adminCash) {
            $adminCash = AdminCash::lockForUpdate()->first();
        }

        // 3. If no record exists at all, create one
        if (!$adminCash) {
            $adminCash = AdminCash::create([
                'admin_id' => $adminId,
                'cash'     => 0,
            ]);
        }

        return $adminCash;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $totalAdminCash = (float) AdminCash::sum('cash');
        return view('pages.admin.cost.create', compact('totalAdminCash'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cost_date' => 'required|date',
            'category' => 'required|in:office,transport,staff,maintenance,salary,product,utility,marketing,miscellaneous',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $costAmount = round((float) $request->amount, 2);

        DB::transaction(function () use ($request, $costAmount) {
            // 1. Deduct cost amount from Admin Cash
            $adminCash = $this->getAdminCashRecord();
            $adminCashBefore = (float) $adminCash->cash;
            $adminCash->decrement('cash', $costAmount);
            $adminCashAfter = round($adminCashBefore - $costAmount, 2);

            // 2. Create Company Cost
            $cc = CompanyCost::create([
                'amount' => $costAmount,
                'cost_date' => $request->cost_date,
                'category' => $request->category,
                'description' => $request->description,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // 3. Log in Admin Cash Ledger
            if (\Illuminate\Support\Facades\Schema::hasTable('admin_cash_ledgers')) {
                \App\Models\AdminCashLedger::create([
                    'admin_id'       => Auth::id(),
                    'type'           => 'company_cost',
                    'amount'         => $costAmount,
                    'cash_before'    => $adminCashBefore,
                    'cash_after'     => $adminCashAfter,
                    'payment_method' => 'cash',
                    'status'         => 'complete',
                    'reference_type' => 'CompanyCost',
                    'reference_id'   => $cc->id,
                    'note'           => "Company Cost: {$request->description}",
                ]);
            }
        });

        return redirect()->route('admin.company_costs.index')
            ->with('success', 'Company cost recorded successfully and deducted from Admin Cash.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cost = CompanyCost::findOrFail($id);
        return view('pages.admin.cost.show', compact('cost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cost = CompanyCost::findOrFail($id);
        $totalAdminCash = (float) AdminCash::sum('cash');
        return view('pages.admin.cost.edit', compact('cost', 'totalAdminCash'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cost = CompanyCost::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cost_date' => 'required|date',
            'category' => 'required|in:office,transport,staff,maintenance,salary,product,utility,marketing,miscellaneous',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $newAmount = round((float) $request->amount, 2);
        $oldAmount = round((float) $cost->amount, 2);
        $diff = round($newAmount - $oldAmount, 2);

        DB::transaction(function () use ($cost, $request, $newAmount, $diff) {
            // 1. Adjust Admin Cash by difference (newAmount - oldAmount)
            if ($diff != 0) {
                $adminCash = $this->getAdminCashRecord();
                if ($diff > 0) {
                    // Cost increased: deduct additional amount from admin cash
                    $adminCash->decrement('cash', $diff);
                } else {
                    // Cost decreased: refund difference back to admin cash
                    $adminCash->increment('cash', abs($diff));
                }
            }

            // 2. Update Company Cost record
            $cost->update([
                'amount' => $newAmount,
                'cost_date' => $request->cost_date,
                'category' => $request->category,
                'description' => $request->description,
                'notes' => $request->notes,
            ]);
        });

        return redirect()->route('admin.company_costs.index')
            ->with('success', 'Company cost updated successfully and Admin Cash adjusted.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cost = CompanyCost::findOrFail($id);

        DB::transaction(function () use ($cost) {
            // 1. Refund cost amount back to Admin Cash
            $refundAmount = round((float) $cost->amount, 2);
            if ($refundAmount > 0) {
                $adminCash = $this->getAdminCashRecord();
                $adminCash->increment('cash', $refundAmount);
            }

            // 2. Delete cost record
            $cost->delete();
        });

        return redirect()->route('admin.company_costs.index')
            ->with('success', 'Company cost deleted successfully and refunded to Admin Cash.');
    }
}
