<?php

namespace App\Http\Controllers\Dso;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{

  /**
   * Show the Delivery Search UI for DSO.
   */
  public function deliveryIndex()
  {
    return view('pages.dso.delivery.index');
  }

  /**
   * AJAX: Search orders for DSO.
   * - No branch restriction — DSO can deliver any company order.
   * - Supports date filter (created_at date).
   * - Search: order_id, id, customer_name, customer_phone.
   */
  public function deliverySearch(Request $request)
  {
    $search = trim($request->input('search', ''));
    $date   = trim($request->input('date', ''));

    if (empty($search) && empty($date)) {
      return response()->json(['orders' => [], 'message' => 'Enter an Order ID or customer name to search.']);
    }

    $query = Order::with(['customer', 'branch']);

    // ── Date filter ────────────────────────────────────────────────
    if (!empty($date)) {
      try {
        $query->whereDate('created_at', Carbon::parse($date)->toDateString());
      } catch (\Exception $e) {
        // ignore invalid date
      }
    }

    // ── Search filter ──────────────────────────────────────────────
    if (!empty($search)) {
      $query->where(function ($q) use ($search) {
        $q->where('order_id', 'like', "%{$search}%")
          ->orWhere('id', $search)
          ->orWhere('customer_name', 'like', "%{$search}%")
          ->orWhere('customer_phone', 'like', "%{$search}%")
          ->orWhereHas('customer', function ($cq) use ($search) {
            $cq->where('shop_name', 'like', "%{$search}%")
              ->orWhere('manager', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
          });
      })->latest()->limit(20);
    } else {
      // Date only — latest 30 orders of that date
      $query->latest()->limit(30);
    }

    $orders = $query->get();

    $label = !empty($search) ? "\"{$search}\"" : "the selected date";

    return response()->json([
      'orders'  => $this->mapOrders($orders),
      'message' => $orders->isEmpty() ? "No orders found for {$label}." : null,
    ]);
  }

  /**
   * Map order collection to response array.
   */
  private function mapOrders($orders): array
  {
    return $orders->map(function ($order) {
      $invoiceUrl = null;
      if (
        $order->order_type == "field_order" ||
        ($order->order_type == "online" && $order->customer?->customer_type == 'wholesale')
      ) {
        $invoiceUrl = route('dso.order.view_invoice', $order->id);
      } elseif ($order->order_type == "retail") {
        $invoiceUrl = route('dso.order.view_retail_invoice', $order->id);
      } elseif (
        $order->order_type == "online" &&
        (empty($order->customer_id) || $order->customer?->customer_type == 'retail')
      ) {
        $invoiceUrl = route('dso.order.view_online_invoice', $order->id);
      } else {
        $invoiceUrl = route('dso.order.view_invoice', $order->id);
      }

      return [
        'id'               => $order->id,
        'order_id'         => $order->order_id ?? ('BRS' . $order->id),
        'order_type'       => $order->order_type,
        'customer_id'      => $order->customer_id,
        'customer_name'    => $order->customer_name ?? ($order->customer->shop_name ?? ($order->customer->manager ?? 'N/A')),
        'customer_phone'   => $order->customer_phone ?? ($order->customer->phone ?? 'N/A'),
        'country'          => $order->country ?? ($order->customer->country ?? 'N/A'),
        'city'             => $order->city ?? ($order->customer->city ?? 'N/A'),
        'address'          => $order->address ?? ($order->customer->address ?? 'N/A'),
        'note'             => $order->note,
        'branch'           => $order->branch->name ?? 'N/A',
        'net_total'        => number_format($order->net_total, 2),
        'payment_amount'   => number_format($order->payment_amount ?? 0, 2),
        'discount_amount'  => number_format($order->discount_amount ?? 0, 2),
        'special_discount' => number_format($order->special_discount ?? 0, 2),
        'payment_method'   => $order->payment_method ?? 'Cash on Delivery',
        'status'           => $order->status,
        'payment_status'   => $order->payment_status ?? 'unpaid',
        'date'             => $order->created_at->format('d M Y, h:i A'),
        'show_url'         => route('dso.order.show', $order->id),
        'delivered_url'    => route('dso.order.delivered', $order->id),
        'invoice_url'      => $invoiceUrl,
        'is_delivered'     => $order->status === 'delivered',
        'is_retail_or_guest' => empty($order->customer_id) || $order->customer?->customer_type === 'retail',
      ];
    })->values()->toArray();
  }


  public function delivered(Request $request, $id)
  {
    $order = Order::with('customer')->findOrFail($id);

    $updateData = [
      'status'       => 'delivered',
      'delivered_by' => auth()->id(),
      'delivered_at' => now(),
    ];

    // Customer নেই OR customer type is retail → auto paid
    if (is_null($order->customer_id) || $order->customer?->customer_type === 'retail') {
      $updateData += [
        'payment_status' => 'paid',
        'payment_amount' => $order->net_total,
      ];
    }

    $order->update($updateData);

    $orderNumber = $order->order_id ?? "BRS{$id}";

    if ($request->ajax() || $request->wantsJson()) {
      return response()->json([
        'success' => true,
        'message' => "Order {$orderNumber} has been delivered successfully.",
        'status'  => 'delivered',
      ]);
    }

    return back()->with('success', "Order {$orderNumber} has been delivered successfully.");
  }

}
