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
    return view('pages.manager.stock.create', compact('suppliers'));
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
          'title'   => 'New Stock-In Request',
          'message' => 'A new stock request has been created by ' . Auth::user()->name,
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

      return back()->with('success', 'Stock request sent successfully!');
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

  // for showing request details
  public function showForAdmin($id)
  {
    $stockRequest = StockInRequest::with(['supplier', 'requestedBy', 'items.product'])->findOrFail($id);
    return view('pages.admin.stock.stock-in-request-show', compact('stockRequest'));
  }

  public function approve($id)
  {
    try {
      DB::transaction(function () use ($id) {
        $request = StockInRequest::with('items', 'requestedBy')->findOrFail($id);


        if ($request->status !== 'pending') {
          throw new \Exception('This request has already been ' . $request->status);
        }

        // stock update
        foreach ($request->items as $item) {
          $stock = \App\Models\Stock::firstOrCreate(
            ['product_id' => $item->product_id],
            ['quantity' => 0]
          );
          $stock->increment('quantity', $item->quantity);
        }

        // update status
        $request->update([
          'status' => 'approved',
          'approved_by' => Auth::id()
        ]);

        // sent notification to the manager
        $notificationData = [
          'title'   => 'Stock Request Approved!',
          'message' => 'Your stock request #' . $request->id . ' has been approved and added to inventory.',
          'url'     => route('manager.stock.show', $request->id),
          'type'    => 'success'
        ];

        $request->requestedBy->notify(new \App\Notifications\SystemNotification($notificationData));
      });

      return back()->with('success', 'Stock approved and manager notified!');
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

      // sent notification to the manager
      $notificationData = [
        'title'   => 'Stock Request Rejected',
        'message' => 'Your stock request #' . $request->id . ' has been rejected by the admin.',
        'url'     => route('manager.stock.show', $request->id),
        'type'    => 'danger'
      ];

      $request->requestedBy->notify(new \App\Notifications\SystemNotification($notificationData));

      return back()->with('error', 'Stock request rejected and manager notified.');
    } catch (\Exception $e) {
      return back()->with('error', 'Something went wrong!');
    }
  }
}
