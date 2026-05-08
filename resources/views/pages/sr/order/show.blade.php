@extends('layouts.srlayout')

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
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
  }

  .info-item label {
    color: var(--text-muted);
    font-size: 0.8rem;
    display: block;
  }

  .info-item p {
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
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
      grid-template-columns: 1fr;
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
      <h3 style="margin:0; color:var(--primary);">Order Detail (BRS{{ $order->id }})</h3>
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

      <span class="request-status-badge" style="background: {{ $bg }}; color: {{ $color }};">
        {{ $text }}
      </span>
    </div>

    @include('components.alert')
  </div>

  <div class="info-grid">
    <div class="info-item">
      <label>Customer</label>
      <p>{{ $order->customer->shop_name ?? 'N/A' }}</p>
    </div>
    <div class="info-item">
      <label>Reference</label>
      <p>{{ $order->sr->fullname ?? 'N/A' }}</p>
    </div>

    <div class="info-item">
      <label>Customer Phone</label>
      <p>{{ $order->customer->phone ?? 'N/A' }}</p>
    </div>
    <div class="info-item">
      <label>Order Date</label>
      <p>{{ $order->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</p>
    </div>

    <div class="info-item">
      <label>Deduction</label>
      <p>{{ number_format($order->applied_deduction_percent, 2) }} %</p>
    </div>

    @if($order->note)
    <div class="info-item" style="grid-column: span 2;">
      <label>Order Note</label>
      <p style="font-weight: 400; font-style: italic; background: var(--primary-soft); padding: 10px; border-radius: 5px; border-left: 3px solid var(--primary);">
        {{ $order->note }}
      </p>
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
          <td class="text-danger">
            @if($item->discount_amount > 0 && $item->selling_rate > 0)
            ({{ number_format(($item->discount_amount / $item->selling_rate) * 100, 2) }}%)
            @else
            -
            @endif
          </td>
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
          <p>@if($item->discount_amount > 0 && $item->selling_rate > 0)
            ({{ number_format(($item->discount_amount / $item->selling_rate) * 100, 2) }}%)
            @else
            -
            @endif
          </p>
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
    <a href="{{ route('sr.order.edit', $order->id) }}" class="btn-smart btn-blue">
      <i class="fas fa-edit"></i> Edit
    </a>

    @elseif($order->status == 'complete')
    <a href="{{ route('sr.order.view_invoice', $order->id) }}" class="btn-smart btn-green">
      <i class="fas fa-file-invoice"></i>Invoice
    </a>


    <form action="{{ route('sr.order.delivered', $order->id) }}" method="POST">
      @csrf
      @method('PATCH')

      <button type="submit" class="btn-smart btn-blue"
        onclick="return confirm('আপনি কি নিশ্চিত, এই অর্ডারটি ডেলিভার করা হয়েছে?')">

        <i class="fas fa-truck-check"></i>Confirm Delivered
      </button>
    </form>

    @elseif($order->status == 'delivered')
    <a href="{{ route('sr.order.view_invoice', $order->id) }}" class="btn-smart btn-green">
      <i class="fas fa-file-invoice"></i>Invoice
    </a>
    @endif
  </div>
</div>

<div style="text-align: center; margin-top: 20px;">
  <a href="{{ route('sr.order.index') }}" style="color: var(--text-muted); text-decoration: none;">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>
@endsection