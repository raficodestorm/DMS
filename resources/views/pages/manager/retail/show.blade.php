@extends('layouts.managerlayout')

@section('content')
<style>
  .show-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, .05);
    margin-bottom: 18px;
  }

  .detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
    font-family: 'Inter', sans-serif;
    font-size: 13px;
  }

  .detail-row:last-child {
    border-bottom: none;
  }

  .detail-label {
    color: var(--text-muted);
    font-weight: 600;
  }

  .detail-value {
    color: var(--text-main);
    font-weight: 700;
  }

  .items-table {
    width: 100%;
    border-collapse: collapse;
  }

  .items-table th {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: var(--primary);
    padding: 10px 14px;
    background: var(--primary-soft);
    border-bottom: 1px solid var(--border-color);
    font-family: 'Inter', sans-serif;
  }

  .items-table td {
    padding: 11px 14px;
    font-size: 13px;
    color: var(--text-main);
    border-bottom: 1px solid var(--border-color);
    font-family: 'Inter', sans-serif;
  }

  .items-table tr:last-child td {
    border-bottom: none;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 700;
    padding: 5px 13px;
    border-radius: 50px;
    font-family: 'Inter', sans-serif;
  }

  .pill-approved {
    background: rgba(16, 185, 129, .12);
    color: #10b981;
  }

  .pill-complete {
    background: rgba(49, 49, 255, .1);
    color: var(--primary);
  }

  .pill-delivered {
    background: rgba(79, 172, 254, .12);
    color: #4facfe;
  }

  .pill-rejected {
    background: rgba(239, 68, 68, .12);
    color: #ef4444;
  }

  .action-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 7px 14px;
    border-radius: 9px;
    text-decoration: none;
    transition: opacity .2s;
  }

  .btn-back {
    background: rgba(100, 116, 139, .1);
    color: #64748b;
  }

  .btn-edit {
    background: rgba(245, 158, 11, .1);
    color: #f59e0b;
  }

  .btn-del {
    background: rgba(239, 68, 68, .1);
    color: #ef4444;
    border: none;
    cursor: pointer;
  }

  .action-btn:hover {
    opacity: .75;
  }
</style>

<div class="show-card animate__animated animate__fadeIn">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <h2 style="font-size:20px; font-family:'Cinzel',serif; color:var(--primary); font-weight:700; margin:0;">
        <i class="fas fa-store me-2"></i>Retail Order — BRS{{ $order->id }}
      </h2>
      <p style="font-size:12px; color:var(--text-muted); margin:4px 0 0; font-family:'Inter',sans-serif;">
        Created {{ $order->created_at->format('d M Y, h:i A') }}
      </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('manager.retail.index') }}" class="action-btn btn-back">
        <i class="fas fa-arrow-left"></i> Back
      </a>
      <a href="{{ route('manager.order.view_invoice', $order->id) }}" class="action-btn" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
        <i class="fas fa-print"></i> Invoice
      </a>
      @if(!in_array($order->status, ['complete','delivered']))
      <a href="{{ route('manager.retail.edit', $order->id) }}" class="action-btn btn-edit">
        <i class="fas fa-pen"></i> Edit
      </a>
      <form method="POST" action="{{ route('manager.retail.destroy', $order->id) }}" onsubmit="return confirm('Delete this retail order permanently?')">
        @csrf @method('DELETE')
        <button type="submit" class="action-btn btn-del">
          <i class="fas fa-trash"></i> Delete
        </button>
      </form>
      @endif
    </div>
  </div>

  {{-- Order Details --}}
  <div class="detail-row">
    <span class="detail-label">Customer</span>
    <span class="detail-value">{{ $order->customer->shop_name ?? '—' }}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">Status</span>
    <span>
      <span class="status-pill pill-{{ $order->status }}">
        <i class="fas fa-circle" style="font-size:6px;"></i>
        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
      </span>
    </span>
  </div>
  <div class="detail-row">
    <span class="detail-label">Custom Deduction</span>
    <span class="detail-value">{{ $order->applied_deduction_percent ?? 0 }}%</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">Discount Amount</span>
    <span class="detail-value">৳ {{ number_format($order->discount_amount, 2) }}</span>
  </div>
  @if($order->special_discount > 0)
  <div class="detail-row">
    <span class="detail-label">Special Discount</span>
    <span class="detail-value" style="color:#f59e0b;">৳ {{ number_format($order->special_discount, 2) }}</span>
  </div>
  @endif
  <div class="detail-row">
    <span class="detail-label" style="font-size:15px;">Net Total</span>
    <span class="detail-value" style="font-size:18px; color:var(--primary);">
      ৳ {{ number_format($order->net_total, 2) }}
    </span>
  </div>
  @if($order->note)
  <div class="detail-row">
    <span class="detail-label">Note</span>
    <span class="detail-value" style="font-style:italic; color:var(--text-muted);">{{ $order->note }}</span>
  </div>
  @endif
</div>

{{-- Order Items --}}
<div class="show-card animate__animated animate__fadeIn" style="animation-delay: 0.1s;">
  <h5 style="font-family:'Inter',sans-serif; font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--primary); margin-bottom:14px;">
    <i class="fas fa-boxes-stacked me-2"></i>Order Items ({{ $order->items->count() }})
  </h5>
  <div style="overflow-x:auto;">
    <table class="items-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Product</th>
          <th>Base Price</th>
          <th>Selling Rate</th>
          <th>Offer Disc</th>
          <th>Qty</th>
          <th style="text-align:right;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $i => $item)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td><strong>{{ $item->product->name ?? '—' }}</strong></td>
          <td>৳ {{ number_format($item->price, 2) }}</td>
          <td>৳ {{ number_format($item->selling_rate, 2) }}</td>
          <td>৳ {{ number_format($item->discount_amount, 2) }}</td>
          <td>{{ $item->quantity }}</td>
          <td style="text-align:right; font-weight:700; color:var(--primary);">
            ৳ {{ number_format($item->net_total, 2) }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
