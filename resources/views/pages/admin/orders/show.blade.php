@extends('layouts.adminlayout')

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
    font-weight: 600;
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

  /* Profit Reveal */
  .profit-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    margin-top: 12px;
  }

  #btn-see-profit {
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff;
    border: none;
    padding: 10px 22px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    transition: transform 0.2s, box-shadow 0.2s;
  }

  #btn-see-profit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
  }

  #profit-reveal {
    display: none;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: 1.5px solid #34d399;
    border-radius: 12px;
    padding: 10px 20px;
    animation: profitPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  #profit-reveal .profit-label {
    font-size: 13px;
    color: #065f46;
    font-weight: 600;
  }

  #profit-reveal .profit-amount {
    font-size: 22px;
    font-weight: 800;
    color: #065f46;
    letter-spacing: -0.5px;
  }

  #profit-reveal .profit-timer {
    font-size: 11px;
    color: #6ee7b7;
    margin-left: 4px;
    font-weight: 600;
  }

  @keyframes profitPop {
    from { opacity: 0; transform: scale(0.85) translateY(6px); }
    to   { opacity: 1; transform: scale(1)   translateY(0); }
  }

  @media (max-width: 600px) {
    .info-grid {
      grid-template-columns: 1fr;
    }

    .action-bar {
      flex-direction: column;
    }

    .profit-wrapper {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>

<div class="manage-card">
  <div class="card-header">
    <div class="request-header-box">
      <h3 style="margin:0; color:var(--primary);">Order Details ({{ $order->order_id ?? ('BRS' . $order->id) }})</h3>
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
      <p>{{ $order->customer->shop_name ?? 'Retail' }}</p>
    </div>
    <div class="info-item">
      <label>Reference</label>
      <p>{{ $order->sr->fullname ?? $order->manager->fullname }} <span class="text-primary"> ({{ $order->branch->name ?? 'N/A' }}
          branch)</span>
      </p>
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

    <div class="info-item">
      <label>Order Type</label>
      <p>
        @if($order->order_type == "field_order")
        <span class="emerald-type-badge">Field Order</span>
        @elseif($order->order_type == 'retail')
        <span class="pink-type-badge">Retail</span>
        @elseif($order->order_type == 'online')
        <span class="purple-type-badge">Online</span>
        @else
        <span class="status-undefined-badge">Undefined</span>
        @endif
      </p>
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
          <td>{{ $item->product->name }}</td>
          <td>{{ number_format($item->price, 2) }} ৳</td>
          <td>{{ number_format($item->selling_rate, 2) }} ৳</td>
          <td>{{ $item->quantity }}</td>
          <td class="text-danger">@if($item->discount_amount > 0 && $item->selling_rate > 0)
            ({{ number_format(($item->discount_amount / $item->selling_rate) * 100, 2) }}%)
            @else
            -
            @endif</td>
          <td>{{ number_format($item->net_total, 2) }} ৳</td>
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

    @php $totalProfit = $order->items->sum('profit'); @endphp

    <div class="profit-wrapper" >
      <button id="btn-see-profit" onclick="revealProfit()">
        <i class="fas fa-chart-line"></i> See Profit
      </button>

      <div id="profit-reveal">
        <i class="fas fa-sack-dollar" style="color:#059669; font-size:20px;"></i>
        <div>
          <div class="profit-label">Order Profit</div>
          <div class="profit-amount">৳ {{ number_format($totalProfit, 2) }}</div>
        </div>
        <span class="profit-timer" id="profit-countdown"></span>
      </div>
    </div>
  </div>

  <script>
    function revealProfit() {
      var btn    = document.getElementById('btn-see-profit');
      var reveal = document.getElementById('profit-reveal');
      var countdown = document.getElementById('profit-countdown');
      var seconds = 2;

      btn.style.display = 'none';
      reveal.style.display = 'flex';
      countdown.textContent = '(' + seconds + 's)';

      var timer = setInterval(function () {
        seconds--;
        if (seconds <= 0) {
          clearInterval(timer);
          reveal.style.transition = 'opacity 0.4s';
          reveal.style.opacity = '0';
          setTimeout(function () {
            reveal.style.display = 'none';
            reveal.style.opacity = '1';
            reveal.style.transition = '';
            btn.style.display = 'inline-flex';
          }, 400);
        } else {
          countdown.textContent = '(' + seconds + 's)';
        }
      }, 1000);
    }
  </script>


  <div class="action-bar">

    @if($order->status == 'pending_manager')

        <form action="{{ route('admin.order.reject', $order->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <button type="submit"
                class="btn-smart btn-reject"
                onclick="return confirm('Reject this order?')">

                <i class="fas fa-times-circle"></i> Reject
            </button>
        </form>

        <form action="{{ route('admin.order.approve', $order->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <button type="submit" class="btn-smart btn-admin">
                <i class="fas fa-check-circle"></i> Approve
            </button>
        </form>

    @elseif(in_array($order->status, ['complete', 'delivered']))

        @if($order->order_type == "field_order")

            <a href="{{ route('admin.order.view_invoice', $order->id) }}"
                class="btn-smart btn-green">

                <i class="fas fa-file-invoice"></i> Invoice
            </a>

        @elseif($order->order_type == "retail")

            <a href="{{ route('admin.order.view_retail_invoice', $order->id) }}"
                class="btn-smart btn-green">

                <i class="fas fa-file-invoice"></i> Invoice
            </a>

        @endif

    @endif

</div>
</div>

<div style="text-align: center; margin-top: 20px;">
  <a href="{{ route('admin.order.index') }}" style="color: var(--text-muted); text-decoration: none;">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>
@endsection