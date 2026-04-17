<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockInItem;
use App\Models\StockInRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockRequestController extends Controller
{
  public function createStockRequest()
  {
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    return view('pages.manager.stock.stock-in-create', compact('suppliers'));
  }

  public function getProductsBySupplier($supplier_id)
  {
    $products = Product::where('supplier_id', $supplier_id)
      ->select('id', 'supplier_id', 'name', 'price')
      ->orderBy('name', 'asc')
      ->get();

    return response()->json($products);
  }


  public function store(Request $request)
  {

    $request->validate([
      'supplier_id' => 'required|exists:suppliers,id',
      'products' => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.qty' => 'required|integer|min:1',
      'net_total' => 'required|numeric|min:0'
    ]);

    try {
      DB::transaction(function () use ($request) {
        $stockRequest = StockInRequest::create([
          'supplier_id' => $request->supplier_id,
          'requested_by' => Auth::id(),
          'net_total' => $request->net_total,
          'status' => 'pending'
        ]);

        foreach ($request->products as $item) {
          $product = \App\Models\Product::find($item['product_id']);

          StockInItem::create([
            'stock_in_request_id' => $stockRequest->id,
            'product_id' => $item['product_id'],
            'quantity' => $item['qty'],
            'cost_price' => $product->price
          ]);
        }

        // --- notification data as array ---
        $notificationData = [
          'title'   => 'Stock-In Request',
          'message' => [
            'text' => 'A new stock-in request from',
            'from' => Auth::user()->branch->name
          ],
          'url'     => route('admin.stock.in.request.show', $stockRequest->id),
          'type'    => 'stock_request'
        ];

        // 🔔 Notify Admin
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
          // sent by $notificationData array
          $admin->notify(new \App\Notifications\SystemNotification($notificationData));
        }
      });

      return redirect()->route('manager.stock.in.requests.index')->with('success', 'Stock request sent successfully!');
    } catch (\Exception $e) {
      dd($e);
    }
  }

  // for showing all pending requests
  public function stockInRequestIndexForAdmin()
  {
    $requests = StockInRequest::with(['supplier', 'requestedBy'])
      ->orderBy('created_at', 'desc')
      ->get();
    return view('pages.admin.stock.stock-in-requests-index', compact('requests'));
  }

  // for showing all pending requests
  public function stockInRequestIndexForManager()
  {
    $requests = StockInRequest::with(['supplier', 'requestedBy'])
      ->where('requested_by', auth()->user()->id)
      ->orderBy('created_at', 'desc')
      ->get();
    return view('pages.manager.stock.stock-in-requests-index', compact('requests'));
  }

  // for showing request details
  public function showForAdmin($id)
  {
    $request = StockInRequest::with(['supplier', 'requestedBy', 'items.product'])->findOrFail($id);
    return view('pages.admin.stock.stock-in-request-show', compact('request'));
  }


  // ম্যানেজারের জন্য শো পেজ
  public function showForManager($id)
  {
    $request = StockInRequest::with(['supplier', 'items.product'])->findOrFail($id);
    return view('pages.manager.stock.stock-in-request-show', compact('request'));
  }

  // রিকোয়েস্ট ডিলিট করা
  public function stockInDestroy($id)
  {
    $request = StockInRequest::findOrFail($id);

    if ($request->status == 'approved') {
      return back()->with('error', 'Approved requests cannot be deleted.');
    }

    DB::transaction(function () use ($request) {
      $request->items()->delete();
      $request->delete();
    });

    return redirect()->route('manager.stock.in.requests.index')->with('success', 'Request deleted successfully.');
  }


  public function stockInEdit($id)
  {
    $request = StockInRequest::findOrFail($id);
    $suppliers = Supplier::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
    if ($request->status == 'approved') {
      return back()->with('error', 'You cannot edit approved request.');
    }

    return view('pages.manager.stock.stock-in-edit', compact('request', 'suppliers'));
  }


  public function stockInUpdate(Request $request, $id)
  {
    $request->validate([
      'supplier_id' => 'required|exists:suppliers,id',
      'products' => 'required|array|min:1',
      'products.*.product_id' => 'required|exists:products,id',
      'products.*.qty' => 'required|integer|min:1',
      'net_total' => 'required|numeric|min:0'
    ]);

    try {
      return DB::transaction(function () use ($request, $id) {
        $stockRequest = StockInRequest::where('requested_by', Auth::id())
          ->findOrFail($id);

        if ($stockRequest->status !== 'pending') {
          return back()->with('error', 'Only pending requests can be updated.');
        }

        $stockRequest->update([
          'supplier_id' => $request->supplier_id,
          'net_total' => $request->net_total,
        ]);

        $stockRequest->items()->delete();

        $itemsToInsert = [];
        foreach ($request->products as $item) {

          $product = \App\Models\Product::find($item['product_id']);

          $itemsToInsert[] = [
            'stock_in_request_id' => $stockRequest->id,
            'product_id' => $item['product_id'],
            'quantity' => $item['qty'],
            'cost_price' => $product->price,
            'created_at' => now(),
            'updated_at' => now(),
          ];
        }

        StockInItem::insert($itemsToInsert);

        return redirect()->route('manager.stock.in.requests.index')->with('success', 'Stock request updated successfully!');
      });
    } catch (\Exception $e) {
      return back()->with('error', 'Error: ' . $e->getMessage());
    }
  }

  public function approve($id)
  {
    try {
      DB::transaction(function () use ($id) {

        $request = StockInRequest::with(['items', 'requestedBy'])->findOrFail($id);

        if ($request->status !== 'pending') {
          throw new \Exception('This request has already been ' . $request->status);
        }

        $targetBranchId = $request->requestedBy->branch_id;

        if (!$targetBranchId) {
          throw new \Exception('The requesting user is not assigned to any branch.');
        }

        foreach ($request->items as $item) {
          $stock = \App\Models\Stock::firstOrCreate(
            [
              'product_id' => $item->product_id,
              'branch_id'  => $targetBranchId
            ],
            ['quantity' => 0]
          );

          $stock->increment('quantity', $item->quantity);
        }

        $request->update([
          'status' => 'approved',
          'approved_by' => Auth::id()
        ]);
      });

      return redirect()->route('dashboards')->with('success', 'Approved and branch stock updated!');
    } catch (\Exception $e) {
      return back()->with('error', $e->getMessage());
    }
  }

  public function reject($id)
  {
    try {
      $request = StockInRequest::with('requestedBy')->findOrFail($id);

      if ($request->status !== 'pending') {
        return back()->with('error', 'Only pending requests can be rejected.');
      }

      $request->update(['status' => 'rejected']);

      return redirect()->route('dashboards')->with('success', 'Request rejected!');
    } catch (\Exception $e) {
      return back()->with('error', 'Something went wrong!');
    }
  }
}
