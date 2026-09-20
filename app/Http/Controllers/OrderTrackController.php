<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackController extends Controller
{
    /**
     * Search and track order status by Order ID.
     */
    public function track(Request $request)
    {
        $queryInput = trim((string) $request->input('query', ''));

        if (empty($queryInput)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter your Order ID.'
            ], 422);
        }

        $cleanId = preg_replace('/^BRS/i', '', $queryInput);

        // Find order matching order_id or ID
        $order = Order::with('customer')
            ->where(function ($q) use ($queryInput, $cleanId) {
                $q->where('order_id', $queryInput)
                  ->orWhere('id', $queryInput)
                  ->orWhere('id', $cleanId)
                  ->orWhere('order_id', 'BRS' . $cleanId);
            })
            ->latest()
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => "No order found with Order ID \"{$queryInput}\"."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order'   => $this->formatOrderTrackingData($order),
        ]);
    }

    /**
     * Format an order object with tracking steps and summary payload.
     */
    protected function formatOrderTrackingData(Order $order): array
    {
        $status = strtolower((string) $order->status);
        $isCancelled = in_array($status, ['rejected', 'cancel', 'canceled', 'cancelled']);

        $isPlaced     = true;
        $isConfirmed  = in_array($status, ['pending_manager', 'approved', 'complete', 'completed', 'delivered']);
        $isProcessing = in_array($status, ['approved', 'complete', 'completed', 'delivered']);
        $isShipped    = in_array($status, ['complete', 'completed', 'delivered']);
        $isDelivered  = in_array($status, ['delivered']);

        $statusMeta = $this->getStatusMeta($status, $isCancelled);

        // Timeline steps
        $steps = [];

        // 1. Placed Step
        $steps[] = [
            'key'       => 'placed',
            'title'     => 'Placed',
            'subtitle'  => 'Order received successfully',
            'icon'      => 'fas fa-receipt',
            'completed' => true,
            'active'    => ($status === 'pending_sr'),
            'date'      => $order->created_at ? $order->created_at->format('d M Y, h:i A') : null,
        ];

        // 2. Cancelled Step (Only shown if cancelled)
        if ($isCancelled) {
            $steps[] = [
                'key'       => 'cancelled',
                'title'     => 'Cancelled',
                'subtitle'  => $order->note ?? 'Order could not be processed',
                'icon'      => 'fas fa-times-circle',
                'completed' => true,
                'active'    => true,
                'is_danger' => true,
                'date'      => $order->updated_at ? $order->updated_at->format('d M Y, h:i A') : null,
            ];
        } else {
            // 3. Confirmed Step
            $steps[] = [
                'key'       => 'confirmed',
                'title'     => 'Confirmed',
                'subtitle'  => 'Verified & confirmed by manager',
                'icon'      => 'fas fa-clipboard-check',
                'completed' => $isConfirmed,
                'active'    => ($status === 'pending_manager'),
                'date'      => ($isConfirmed && $order->updated_at) ? $order->updated_at->format('d M Y') : null,
            ];

            // 4. Processing Step
            $steps[] = [
                'key'       => 'processing',
                'title'     => 'Processing',
                'subtitle'  => 'Preparing products for dispatch',
                'icon'      => 'fas fa-boxes-packing',
                'completed' => $isProcessing,
                'active'    => ($status === 'approved'),
                'date'      => null,
            ];

            // 5. Shipped Step
            $steps[] = [
                'key'       => 'shipped',
                'title'     => 'Shipped',
                'subtitle'  => 'Dispatched to delivery destination',
                'icon'      => 'fas fa-truck-fast',
                'completed' => $isShipped,
                'active'    => in_array($status, ['complete', 'completed']),
                'date'      => null,
            ];

            // 6. Delivered Step
            $steps[] = [
                'key'       => 'delivered',
                'title'     => 'Delivered',
                'subtitle'  => 'Package handed over to customer',
                'icon'      => 'fas fa-house-circle-check',
                'completed' => $isDelivered,
                'active'    => ($status === 'delivered'),
                'date'      => $order->delivered_at ? $order->delivered_at->format('d M Y, h:i A') : null,
            ];
        }

        return [
            'id'                  => $order->id,
            'order_id'            => $order->display_order_id,
            'customer_name'       => $order->customer_name ?? ($order->customer ? $order->customer->name : 'Customer'),
            'customer_phone'      => $order->customer_phone ?? ($order->customer ? $order->customer->phone : ''),
            'delivery_address'    => trim(($order->address ?? '') . ' ' . ($order->city ?? '') . ' ' . ($order->country ?? '')),
            'status'              => $status,
            'status_label'        => $statusMeta['label'],
            'status_color'        => $statusMeta['color'],
            'status_bg'           => $statusMeta['bg'],
            'is_cancelled'        => $isCancelled,
            'payment_status'      => ucfirst($order->payment_status ?? 'Pending'),
            'payment_method'      => strtoupper($order->payment_method ?? 'COD'),
            'net_total'           => (float) $order->net_total,
            'net_total_formatted' => '৳' . number_format((float) $order->net_total, 2),
            'created_at'          => $order->created_at ? $order->created_at->format('d M Y, h:i A') : '',
            'delivered_at'        => $order->delivered_at ? $order->delivered_at->format('d M Y, h:i A') : null,
            'steps'               => $steps,
            'total_items_count'   => (int) $order->items()->sum('quantity'),
        ];
    }

    /**
     * Get badge color & label metadata for status.
     */
    protected function getStatusMeta(string $status, bool $isCancelled): array
    {
        if ($isCancelled) {
            return ['label' => 'Cancelled', 'color' => '#dc2626', 'bg' => 'rgba(239, 68, 68, 0.14)'];
        }

        return match ($status) {
            'pending_sr'      => ['label' => 'Placed', 'color' => '#2563eb', 'bg' => 'rgba(37, 99, 235, 0.14)'],
            'pending_manager' => ['label' => 'Confirmed', 'color' => '#8b5cf6', 'bg' => 'rgba(139, 92, 246, 0.14)'],
            'approved'        => ['label' => 'Processing', 'color' => '#d97706', 'bg' => 'rgba(245, 158, 11, 0.14)'],
            'complete',
            'completed'       => ['label' => 'Shipped', 'color' => '#0891b2', 'bg' => 'rgba(8, 145, 178, 0.14)'],
            'delivered'       => ['label' => 'Delivered', 'color' => '#059669', 'bg' => 'rgba(5, 150, 105, 0.14)'],
            default           => ['label' => ucfirst(str_replace('_', ' ', $status)), 'color' => '#0e69fc', 'bg' => 'rgba(14, 105, 252, 0.12)'],
        };
    }
}
