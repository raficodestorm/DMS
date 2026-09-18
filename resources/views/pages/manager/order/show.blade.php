@extends('layouts.managerlayout')

@section('content')
<style>
  .request-header-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 2px solid var(--background);
    padding-bottom: 15px;
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 25px;
  }

  .info-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    transition: border-color 0.2s ease, transform 0.15s ease;
  }

  .info-card:hover {
    border-color: var(--primary);
  }

  .info-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }

  .info-details {
    min-width: 0;
    flex: 1;
  }

  .info-details label {
    color: var(--text-muted);
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 2px;
  }

  .info-details p {
    font-weight: 600;
    font-size: 0.92rem;
    color: var(--text-main);
    margin: 0;
    word-break: break-word;
  }

  .action-bar {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    justify-content: flex-end;
    flex-wrap: wrap;
  }



  .request-status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
  }

  @media (max-width: 600px) {
    .info-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }

    .info-card {
      padding: 7px 8px;
      gap: 7px;
      border-radius: 8px;
    }

    .info-icon-box {
      width: 30px;
      height: 30px;
      font-size: 12px;
      border-radius: 6px;
    }

    .info-details label {
      font-size: 0.6rem;
    }

    .info-details p {
      font-size: 0.75rem;
    }

    .info-card.span-2 {
      grid-column: span 2 !important;
    }

    .action-bar {
      flex-direction: column;
    }

    .btn-smart {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<div class="manage-card">
  <div class="card-header">
    <div class="request-header-box">
      <h3 style="margin:0; color:var(--primary);">Order Detail ({{ $order->order_id ?? ('BRS' . $order->id) }})</h3>
      @php
      $bg = '#f3f4f6';
      $color = '#6b7280';
      $text = 'Undefined';

      if($order->status == 'pending_sr'){
      $bg = '#fffbeb';
      $color = '#d97706';
      $text = 'Pending SR';
      }
      elseif($order->status == 'pending_manager'){
      $bg = '#dbeafe';
      $color = '#1d4ed8';
      $text = 'Pending Manager';
      }
      elseif($order->status == 'approved'){
      $bg = '#dcfce7';
      $color = '#16a34a';
      $text = 'Approved';
      }
      elseif($order->status == 'rejected'){
      $bg = '#fee2e2';
      $color = '#dc2626';
      $text = 'Rejected';
      }
      elseif($order->status == 'complete'){
      $bg = '#ede9fe';
      $color = '#6d28d9';
      $text = 'Complete';
      }
      elseif($order->status == 'delivered'){
      $bg = '#dcfce7';
      $color = '#15803d';
      $text = 'Delivered';
      }
      @endphp

      <div>

      @if($order->order_type == "field_order")
          <span class="emerald-type-badge">Field Order</span>
          @elseif($order->order_type == 'retail')
          <span class="pink-type-badge">Retail</span>
          @elseif($order->order_type == 'online')
          <span class="purple-type-badge">Online</span>
          @else
          <span class="status-undefined-badge">Undefined</span>
          @endif


      <span class="request-status-badge" style="background: {{ $bg }}; color: {{ $color }};">
        {{ $text }}
      </span>

       
      </div>
    </div>

    @include('components.alert')
  </div>

  <div class="info-grid">
    <div class="info-card">
      <div class="info-icon-box">
        <i class="fas fa-store"></i>
      </div>
      <div class="info-details">
        <label>Customer</label>
        <p>{{ $order->customer_name ?? $order->customer->shop_name }}</p>
      </div>
    </div>

    <div class="info-card">
      <div class="info-icon-box">
        <i class="fas fa-user-tie"></i>
      </div>
      <div class="info-details">
        <label>Reference</label>
        <p>{{ $order->sr->fullname ?? 'N/A' }}</p>
      </div>
    </div>

    <div class="info-card">
      <div class="info-icon-box">
        <i class="fas fa-phone-alt"></i>
      </div>
      <div class="info-details">
        <label>Customer Phone</label>
        <p>{{ $order->customer_phone ?? $order->customer->phone ?? 'N/A' }}</p>
      </div>
    </div>

    <div class="info-card">
      <div class="info-icon-box">
        <i class="fas fa-calendar-day"></i>
      </div>
      <div class="info-details">
        <label>Order Date</label>
        <p>{{ $order->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</p>
      </div>
    </div>

    <div class="info-card">
      <div class="info-icon-box">
        <i class="fas fa-percentage"></i>
      </div>
      <div class="info-details">
        <label>Deduction</label>
        <p>{{ number_format($order->applied_deduction_percent, 2) }} %</p>
      </div>
    </div>

    <div class="info-card">
      <div class="info-icon-box">
        <i class="fas fa-layer-group"></i>
      </div>
      <div class="info-details">
        <label>Supplier</label>
        <p>{{ $order->supplier->company_name ?? 'N/A' }}</p>
      </div>
    </div>

    <div class="info-card span-2" style="grid-column: span 2;">
      <div class="info-icon-box">
        <i class="fas fa-map-marker-alt"></i>
      </div>
      <div class="info-details">
        <label>Delivery Address</label>
        <p>{{ $order->address ?? 'N/A' }}</p>
      </div>
    </div>

    @if($order->note)
    <div class="info-card span-2" style="grid-column: span 2; background: var(--primary-soft); border-left: 3px solid var(--primary);">
      <div class="info-icon-box" style="background: var(--section-bg);">
        <i class="fas fa-comment-dots"></i>
      </div>
      <div class="info-details">
        <label style="color: var(--primary);">Order Note</label>
        <p style="font-weight: 500; font-style: italic;">
          {{ $order->note }}
        </p>
      </div>
    </div>
    @endif
  </div>

  <h4 style="color:var(--text-muted); border-left: 4px solid var(--primary); padding-left: 10px; margin-bottom: 15px;">
    Order Items</h4>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Product Name</th>
          <th>Base price</th>
          <th>Final price</th>
          <th>Quantity</th>
          <th>Discount</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($order->items as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td class="fw-bold">{{ $item->product->name }}</td>
          <td>{{ number_format($item->price, 2) }} ৳</td>
          <td>{{ number_format($item->selling_rate, 2) }} ৳</td>
          <td>{{ $item->quantity }}</td>
          <td class="text-danger">@if(!empty($item->offer))
            {{ $item->offer }}
            @elseif($item->discount_amount > 0 && $item->selling_rate > 0)
            ({{ number_format(($item->discount_amount / $item->selling_rate) * 100, 2) }}%)
            @else
            -
            @endif</td>
          <td class="fw-bold">{{ number_format($item->net_total, 2) }} ৳</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center">No items found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @foreach($order->items as $item)
    <div class="manage-card" style="margin-bottom: 10px; padding: 10px; border: 1px solid var(--border-color);">
      <div class="card-body">
        <div><span>Product</span>
          <p>{{ $item->product->name }}</p>
        </div>
        <div><span>Base price</span>
          <p>{{ number_format($item->price, 2) }} ৳</p>
        </div>
        <div><span>Final price</span>
          <p>{{ number_format($item->selling_rate, 2) }} ৳</p>
        </div>
        <div><span>Qty</span>
          <p>{{ $item->quantity }}</p>
        </div>
        <div><span>Discount</span>
          <p>@if(!empty($item->offer))
            {{ $item->offer }}
            @elseif($item->discount_amount > 0 && $item->selling_rate > 0)
            ({{ number_format(($item->discount_amount / $item->selling_rate) * 100, 2) }}%)
            @else
            -
            @endif</p>
        </div>
        <div><span>Subtotal</span>
          <p>{{ number_format($item->net_total, 2) }} ৳</p>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div style="text-align: right; margin-top: 25px; border-top: 1px dashed var(--border-color); padding-top: 15px;">
    <small class="mb-1 text-success">Special Discount: {{ number_format($order->special_discount, 2) }} ৳</small>
    <p class="mb-1" style="color: red;">Total Discount: {{ number_format($order->discount_amount, 2) }} ৳</p>
    <h3 style="color: var(--primary); font-weight: 800;">Net Total: {{ number_format($order->net_total, 2) }} ৳</h3>
  </div>

  {{-- 🔘 Button Logic Based on Status --}}
  <div class="action-bar">
    @if($order->status == 'pending_sr')
    {{-- 3 Buttons: Edit, Reject, Send to Admin --}}
    <a href="{{ route('manager.order.edit', $order->id) }}" class="btn-smart btn-blue">
      <i class="fas fa-edit"></i> Edit
    </a>

    <form action="{{ route('manager.order.reject', $order->id) }}" method="POST">
      @csrf @method('PATCH')
      <button type="submit" class="btn-smart btn-orange" onclick="return confirm('Reject this order?')">
        <i class="fas fa-times-circle"></i> Reject
      </button>
    </form>

    <form action="{{ route('manager.order.sendToAdmin', $order->id) }}" method="POST">
      @csrf @method('PATCH')
      <button type="submit" class="btn-smart btn-purple">
        <i class="fas fa-paper-plane"></i> Send to Admin
      </button>
    </form>

    @elseif($order->status == 'rejected')
    {{-- Only Delete Button --}}
    <form action="{{ route('manager.order.destroy', $order->id) }}" method="POST">
      @csrf @method('DELETE')
      <button type="submit" class="btn-smart btn-red" onclick="return confirm('Delete this record permanently?')">
        <i class="fas fa-trash"></i> Delete Order
      </button>
    </form>

    <a href="{{ route('manager.order.edit', $order->id) }}" class="btn-smart btn-blue">
      <i class="fas fa-edit"></i> Edit
    </a>

    @elseif($order->status == 'complete' || $order->status == 'delivered')
    {{-- Invoice Button --}}
  
    @if($order->order_type == "field_order" || ($order->order_type == "online" && $order->customer?->customer_type == 'wholesale'))
            <a href="{{ route('admin.order.view_invoice', $order->id) }}"
                class="btn-smart btn-green">

                <i class="fas fa-file-invoice"></i> Invoice
            </a>

        @elseif($order->order_type == "retail")
            <a href="{{ route('admin.order.view_retail_invoice', $order->id) }}"
                class="btn-smart btn-green">

                <i class="fas fa-file-invoice"></i> Invoice
            </a>
        @elseif( $order->order_type == "online" && (empty($order->customer_id) || $order->customer?->customer_type == 'retail'))
           <a href="{{ route('admin.order.view_online_invoice', $order->id) }}"
                class="btn-smart btn-green">

                <i class="fas fa-file-invoice"></i> Invoice
            </a>
        @endif
    

    @elseif($order->status == 'approved')
    {{-- Confirm & Invoice Button --}}
    @if(empty($order->customer_id) || $order->customer?->customer_type == 'retail')
    <a href="{{ route('manager.order.online_confirm', $order->id) }}" class="btn-smart btn-green">
      <i class="fas fa-file-invoice"></i> Confirm Order & Generate Invoice
    </a>
    @else
    <a href="{{ route('manager.order.confirm', $order->id) }}" class="btn-smart btn-green">
      <i class="fas fa-file-invoice"></i> Confirm Order & Generate Invoice
    </a>

    <a href="{{ route('manager.order.edit', $order->id) }}" class="btn-smart btn-blue">
      <i class="fas fa-edit"></i> Edit
    </a>
    @endif
    @endif
  </div>
</div>

<div style="text-align: center; margin-top: 20px;">
  <a href="{{ route('manager.order.index') }}" style="color: var(--text-muted); text-decoration: none;">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>
@endsection