<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    /**
     * Display a listing of stock transfers.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = StockTransfer::with(['fromBranch', 'toBranch', 'requestedBy']);

        if ($user->role === 'manager') {
            // Manager sees transfers FROM their branch OR TO their branch
            $query->where(function ($q) use ($user) {
                $q->where('from_branch_id', $user->branch_id)
                  ->orWhere('to_branch_id', $user->branch_id);
            });

            // Filter by Transfer Type for Manager (Outgoing vs Incoming)
            if ($request->filled('transfer_type')) {
                $type = $request->transfer_type;
                if ($type === 'outgoing') {
                    $query->where('from_branch_id', $user->branch_id);
                } elseif ($type === 'incoming') {
                    $query->where('to_branch_id', $user->branch_id);
                }
            }
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by Transfer ID or Branch Name
        if ($request->filled('search')) {
            $search = trim($request->search);
            // Clean prefix if user typed BRST123 -> 123
            $cleanId = ltrim(preg_replace('/[^0-9]/', '', $search), '0');

            $query->where(function ($q) use ($search, $cleanId) {
                if ($cleanId) {
                    $q->orWhere('id', $cleanId);
                }
                $q->orWhereHas('fromBranch', function ($bq) use ($search) {
                    $bq->where('name', 'like', "%{$search}%");
                })->orWhereHas('toBranch', function ($bq) use ($search) {
                    $bq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $transfers = $query->latest()->paginate(15)->appends($request->query());
        
        $viewPath = $user->role === 'admin' ? 'pages.admin.stock-transfer.index' : 'pages.manager.stock-transfer.index';
        return view($viewPath, compact('transfers'));
    }

    /**
     * Show the form for creating a new stock transfer.
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->role !== 'manager') {
            abort(403);
        }

        $branches = Branch::where('id', '!=', $user->branch_id)->get();
        
        // Only products that exist in the manager's branch stock
        $availableProducts = Stock::with('product')
            ->where('branch_id', $user->branch_id)
            ->where('quantity', '>', 0)
            ->get();

        return view('pages.manager.stock-transfer.create', compact('branches', 'availableProducts'));
    }

    /**
     * Store a newly created stock transfer in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $transfer = StockTransfer::create([
                    'from_branch_id' => $user->branch_id,
                    'to_branch_id' => $request->to_branch_id,
                    'requested_by' => $user->id,
                    'status' => 'pending',
                    'note' => $request->note,
                ]);

                foreach ($request->products as $item) {
                    // Check if source branch has enough stock
                    $stock = Stock::where('branch_id', $user->branch_id)
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if (!$stock || $stock->quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product ID: " . $item['product_id']);
                    }

                    StockTransferItem::create([
                        'stock_transfer_id' => $transfer->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'note' => $item['note'] ?? null,
                    ]);
                }

                // Notify Admin
                $notificationData = [
                    'title' => 'New Stock Transfer Request',
                    'message' => "A new stock transfer request from {$user->branch->name} to " . Branch::find($request->to_branch_id)->name,
                    'url' => route('admin.stock-transfer.show', $transfer->id),
                    'type' => 'stock_transfer'
                ];

                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new SystemNotification($notificationData));
                }
            });

            return redirect()->route('manager.stock-transfer.index')->with('success', 'Stock transfer request submitted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified stock transfer.
     */
    public function edit($id)
    {
        $transfer = StockTransfer::with('items')->findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'manager' || $transfer->from_branch_id !== $user->branch_id) {
            abort(403);
        }

        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be edited.');
        }

        $branches = Branch::where('id', '!=', $user->branch_id)->get();
        $availableProducts = Stock::with('product')
            ->where('branch_id', $user->branch_id)
            ->where('quantity', '>', 0)
            ->get();

        return view('pages.manager.stock-transfer.edit', compact('transfer', 'branches', 'availableProducts'));
    }

    /**
     * Update the specified stock transfer in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $transfer = StockTransfer::findOrFail($id);

        if ($user->role !== 'manager' || $transfer->from_branch_id !== $user->branch_id) {
            abort(403);
        }

        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be updated.');
        }

        $request->validate([
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $user, $transfer) {
                $transfer->update([
                    'to_branch_id' => $request->to_branch_id,
                    'note' => $request->note,
                ]);

                $transfer->items()->delete();

                foreach ($request->products as $item) {
                    $stock = Stock::where('branch_id', $user->branch_id)
                        ->where('product_id', $item['product_id'])
                        ->first();

                    if (!$stock || $stock->quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product ID: " . $item['product_id']);
                    }

                    StockTransferItem::create([
                        'stock_transfer_id' => $transfer->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'note' => $item['note'] ?? null,
                    ]);
                }
            });

            return redirect()->route('manager.stock-transfer.index')->with('success', 'Stock transfer request updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified stock transfer.
     */
    public function show($id)
    {
        $transfer = StockTransfer::with(['fromBranch', 'toBranch', 'requestedBy', 'items.product'])->findOrFail($id);
        $user = Auth::user();

        $viewPath = $user->role === 'admin' ? 'pages.admin.stock-transfer.show' : 'pages.manager.stock-transfer.show';
        return view($viewPath, compact('transfer'));
    }

    /**
     * Admin approves the transfer.
     */
    public function approve($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        try {
            DB::transaction(function () use ($id) {
                $transfer = StockTransfer::findOrFail($id);
                if ($transfer->status !== 'pending') {
                    throw new \Exception('Transfer is not in pending status.');
                }

                $transfer->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                ]);

                // Notify Destination Branch Managers
                $destinationManagers = User::where('role', 'manager')
                    ->where('branch_id', $transfer->to_branch_id)
                    ->get();

                $notificationData = [
                    'title' => 'Stock Transfer Approved',
                    'message' => "A stock transfer from {$transfer->fromBranch->name} has been approved and is ready to be received.",
                    'url' => route('manager.stock-transfer.show', $transfer->id),
                    'type' => 'stock_transfer'
                ];

                foreach ($destinationManagers as $manager) {
                    $manager->notify(new SystemNotification($notificationData));
                }
            });

            return back()->with('success', 'Stock transfer approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin rejects the transfer.
     */
    public function reject($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $transfer = StockTransfer::findOrFail($id);
        if ($transfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be rejected.');
        }

        $transfer->update(['status' => 'rejected']);
        return back()->with('success', 'Stock transfer rejected.');
    }

    /**
     * Destination Manager receives the stock.
     */
    public function receive($id)
    {
        $user = Auth::user();
        $transfer = StockTransfer::with('items')->findOrFail($id);

        if ($user->role !== 'manager' || $user->branch_id !== $transfer->to_branch_id) {
            abort(403);
        }

        if ($transfer->status !== 'approved') {
            return back()->with('error', 'Only approved transfers can be received.');
        }

        try {
            DB::transaction(function () use ($transfer) {
                foreach ($transfer->items as $item) {
                    // 1. Decrease from source branch
                    $sourceStock = Stock::where('branch_id', $transfer->from_branch_id)
                        ->where('product_id', $item->product_id)
                        ->first();
                    
                    if (!$sourceStock || $sourceStock->quantity < $item->quantity) {
                        throw new \Exception("Insufficient stock in source branch for product: " . ($item->product->name ?? $item->product_id));
                    }
                    $sourceStock->decrement('quantity', $item->quantity);

                    // 2. Increase in destination branch
                    $destStock = Stock::firstOrCreate(
                        ['branch_id' => $transfer->to_branch_id, 'product_id' => $item->product_id],
                        ['quantity' => 0]
                    );
                    $destStock->increment('quantity', $item->quantity);
                }

                $transfer->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            });

            return back()->with('success', 'Stock received and inventory updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified stock transfer from storage.
     */
    public function destroy($id)
    {
        $transfer = StockTransfer::findOrFail($id);
        
        if ($transfer->status === 'completed') {
            return back()->with('error', 'Completed transfers cannot be deleted.');
        }

        // Logic for deletion: if manager or admin, then delete from both tables.
        // items will be deleted automatically due to cascadeOnDelete in migration.
        $transfer->delete();

        return redirect()->back()->with('success', 'Stock transfer deleted successfully.');
    }
}

