<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnManagerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ProductReturn::with(['customer', 'sr', 'order'])
            ->where('branch_id', $user->branch_id)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customer) use ($search) {
                      $customer->where('shop_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('sr', function ($sr) use ($search) {
                      $sr->where('username', 'like', "%{$search}%");
                  });
            });
        }

        $returns = $query->paginate(15);

        if ($request->ajax()) {
            return response()->json([
                'table'  => view('pages.manager.return.table', compact('returns'))->render(),
                'mobile' => view('pages.manager.return.mtable', compact('returns'))->render(),
            ]);
        }

        return view('pages.manager.return.index', compact('returns'));
    }

    public function show($id)
    {
        $return = ProductReturn::with(['customer', 'order', 'items.product', 'sr'])->findOrFail($id);
        
        if ($return->branch_id != auth()->user()->branch_id) {
            abort(403);
        }

        return view('pages.manager.return.show', compact('return'));
    }

    public function forwardToAdmin($id)
    {
        $return = ProductReturn::findOrFail($id);

        if ($return->status != 'pending_manager') {
            return back()->with('error', 'Return already processed.');
        }

        $return->update(['status' => 'pending_admin']);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new SystemNotification([
                'title' => 'Return Approval Request',
                'message' => [
                    'text' => 'A return request has been forwarded by',
                    'from' => auth()->user()->branch->name ?? 'Manager'
                ],
                'url' => route('admin.return.show', $return->id),
                'type' => 'return_approval'
            ]));
        }

        return back()->with('success', 'Return request forwarded to Admin.');
    }

    public function reject($id)
    {
        $return = ProductReturn::findOrFail($id);

        if ($return->status != 'pending_manager') {
            return back()->with('error', 'Cannot reject at this stage.');
        }

        $return->update(['status' => 'rejected']);

        return back()->with('success', 'Return request rejected.');
    }
}
