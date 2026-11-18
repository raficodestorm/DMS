<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCash;
use App\Models\AdminCashLedger;
use App\Models\Bonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BonusController extends Controller
{
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('pages.admin.bonus.index');
    }

    public function fetchBonusesIndexData(Request $request)
    {
        $query = Bonus::with('creator')->latest('bonus_date');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('month')) {
            $query->whereMonth('bonus_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('bonus_date', $request->year);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('bonus_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('bonus_date', '<=', $request->to_date);
        }

        $totalBonus = (clone $query)->sum('amount');
        $bonuses = $query->paginate(15)->withQueryString();

        return response()->json([
            'table'      => view('pages.admin.bonus.table', compact('bonuses'))->render(),
            'mobile'     => view('pages.admin.bonus.mtable', compact('bonuses'))->render(),
            'pagination' => (string) $bonuses->links(),
            'total'      => $bonuses->total(),
            'totalBonus' => number_format($totalBonus, 2) . ' ৳',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $totalAdminCash = (float) AdminCash::sum('cash');
        return view('pages.admin.bonus.create', compact('totalAdminCash'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'bonus_date'  => 'required|date',
            'type'        => 'required|in:incentive,cashback,special,other',
            'description' => 'nullable|string|max:1000',
        ]);

        $amount = round((float) $request->amount, 2);

        DB::transaction(function () use ($request, $amount) {
            // 1. Increment Admin Cash
            $adminCash = $this->getAdminCashRecord();
            $cashBefore = (float) $adminCash->cash;
            $adminCash->increment('cash', $amount);
            $cashAfter = round($cashBefore + $amount, 2);

            // 2. Create Bonus entry
            $bonus = Bonus::create([
                'title'       => $request->title,
                'amount'      => $amount,
                'bonus_date'  => $request->bonus_date,
                'type'        => $request->type,
                'description' => $request->description,
                'created_by'  => Auth::id(),
            ]);

            // 3. Log in Admin Cash Ledger
            if (Schema::hasTable('admin_cash_ledgers')) {
                AdminCashLedger::create([
                    'admin_id'       => Auth::id(),
                    'type'           => 'bonus',
                    'amount'         => $amount,
                    'cash_before'    => $cashBefore,
                    'cash_after'     => $cashAfter,
                    'payment_method' => 'cash',
                    'status'         => 'complete',
                    'reference_type' => 'Bonus',
                    'reference_id'   => $bonus->id,
                    'note'           => 'Bonus (' . ucfirst($bonus->type) . '): ' . $bonus->title . ($bonus->description ? ' - ' . $bonus->description : ''),
                ]);
            }
        });

        return redirect()->route('admin.bonuses.index')
            ->with('success', 'Bonus entry created successfully and Admin Cash updated.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bonus $bonus) 
    {
        return view('pages.admin.bonus.show', compact('bonus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bonus $bonus)
    {
        $totalAdminCash = (float) AdminCash::sum('cash');
        return view('pages.admin.bonus.edit', compact('bonus', 'totalAdminCash'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bonus $bonus)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'bonus_date'  => 'required|date',
            'type'        => 'required|in:incentive,cashback,special,other',
            'description' => 'nullable|string|max:1000',
        ]);

        $newAmount = round((float) $request->amount, 2);
        $oldAmount = round((float) $bonus->amount, 2);
        $diff = round($newAmount - $oldAmount, 2);

        DB::transaction(function () use ($bonus, $request, $newAmount, $diff) {
            // 1. Adjust Admin Cash by difference (newAmount - oldAmount)
            if ($diff != 0) {
                $adminCash = $this->getAdminCashRecord();
                if ($diff > 0) {
                    // Bonus increased: increment admin cash by difference
                    $adminCash->increment('cash', $diff);
                } else {
                    // Bonus decreased: decrement admin cash by difference
                    $adminCash->decrement('cash', abs($diff));
                }
            }

            // 2. Update Bonus record
            $bonus->update([
                'title'       => $request->title,
                'amount'      => $newAmount,
                'bonus_date'  => $request->bonus_date,
                'type'        => $request->type,
                'description' => $request->description,
            ]);

            // 3. Update AdminCashLedger entry if exists
            if (Schema::hasTable('admin_cash_ledgers')) {
                $ledger = AdminCashLedger::where('reference_type', 'Bonus')
                    ->where('reference_id', $bonus->id)
                    ->first();

                if ($ledger) {
                    $cashBefore = (float) $ledger->cash_before;
                    $cashAfter  = round($cashBefore + $newAmount, 2);

                    $ledger->update([
                        'amount'     => $newAmount,
                        'cash_after' => $cashAfter,
                        'note'       => 'Bonus (' . ucfirst($bonus->type) . '): ' . $bonus->title . ($bonus->description ? ' - ' . $bonus->description : ''),
                    ]);
                }
            }
        });

        return redirect()->route('admin.bonuses.index')
            ->with('success', 'Bonus entry updated successfully and Admin Cash adjusted.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bonus $bonus)
    {
        DB::transaction(function () use ($bonus) {
            // 1. Deduct previously credited bonus amount from Admin Cash
            $refundAmount = round((float) $bonus->amount, 2);
            if ($refundAmount > 0) {
                $adminCash = $this->getAdminCashRecord();
                $adminCash->decrement('cash', $refundAmount);
            }

            // 2. Remove AdminCashLedger record
            if (Schema::hasTable('admin_cash_ledgers')) {
                AdminCashLedger::where('reference_type', 'Bonus')
                    ->where('reference_id', $bonus->id)
                    ->delete();
            }

            // 3. Delete bonus record
            $bonus->delete();
        });

        return redirect()->route('admin.bonuses.index')
            ->with('success', 'Bonus entry deleted successfully and Admin Cash updated.');
    }
}
