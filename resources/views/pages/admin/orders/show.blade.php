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
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }

    .info-card {
      padding: 7px 8px;
      gap: 8px;
      border-radius: 8px;
    }

    .info-icon-box {
      width: 30px;
      height: 30px;
      font-size: 12px;
      border-radius: 6px;
    }

    .info-details label {
      font-size: 0.60rem;
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
        <p>{{ $order->customer_name ?: ($order->customer?->shop_name ?? 'N/A') }}</p>
      </div>
    </div>

    <div class="info-card">
      <div class="info-icon-box">
        <i class="fas fa-user-tie"></i>
      </div>
      <div class="info-details">
        <label>Reference</label>
        <p>{{ $order->sr->fullname ?? $order->manager->fullname ?? "Online" }} <span class="text-primary" style="font-size: 0.85em; font-weight: 500;">({{ $order->branch->name ?? 'N/A' }} branch)</span></p>
      </div>
    </div>

    <div class="info-card">
      <div class="info-icon-box">
        <i class="fas fa-phone-alt"></i>
      </div>
      <div class="info-details">
        <label>Customer Phone</label>
        <p>{{ $order->customer_phone ?: ($order->customer?->phone ?? 'N/A') }}</p>
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
          <td>{{ $item->product->name }}</td>
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
                class="btn-smart btn-red"
                onclick="return confirm('Reject this order?')">

                <i class="fas fa-times-circle"></i> Reject
            </button>
        </form>

        @if($order->order_type == 'online' && empty($order->customer_id))
            <button type="button" class="btn-smart btn-green" onclick="openAssignBranchModal()">
                <i class="fas fa-check-circle"></i> Approve
            </button>
        @else
            <form action="{{ route('admin.order.approve', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <button type="submit" class="btn-smart btn-green">
                    <i class="fas fa-check-circle"></i> Approve
                </button>
            </form>
        @endif
    @endif


    @if($order->status == 'complete' || $order->status == 'delivered')
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
    
    @endif

   

</div>
</div>

@if($order->order_type == 'online' && empty($order->customer_id))
@php
  $branches = $branches ?? \App\Models\Branch::select('id', 'name')->orderBy('name', 'asc')->get();
@endphp
<!-- Assign Branch & Approve Modal for Online Orders -->
<div id="assignBranchModal" class="assign-modal-overlay" onclick="handleAssignModalBackdrop(event)">
  <div class="assign-modal-box">
    <div class="assign-modal-header">
      <h4>
        <i class="fas fa-code-branch text-primary"></i> Assign Branch & Approve
      </h4>
      <button type="button" class="assign-modal-close-btn" onclick="closeAssignBranchModal()">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <form action="{{ route('admin.order.online_approve', $order->id) }}" method="POST">
      @csrf
      @method('PATCH')

      <div class="assign-order-info">
        <div class="assign-order-info-row">
          <span>Order ID:</span>
          <strong>{{ $order->order_id ?? ('BRS' . $order->id) }}</strong>
        </div>
        <div class="assign-order-info-row">
          <span>Customer:</span>
          <strong>{{ $order->customer_name }} ({{ $order->city }}, {{ $order->country }})</strong>
        </div>
        <div class="assign-order-info-row" style="margin-bottom: 0;">
          <span>Net Total:</span>
          <strong style="color: var(--primary);">৳ {{ number_format($order->net_total, 2) }}</strong>
        </div>
      </div>

      <div class="assign-form-group">
        <label for="branch_id">
          <i class="fas fa-building text-primary"></i> Select Delivery Branch:
        </label>
        <select name="branch_id" id="branch_id" class="assign-branch-select" required>
          <option value="">-- Choose Branch --</option>
          @foreach($branches as $branch)
          <option value="{{ $branch->id }}" {{ ($order->branch_id == $branch->id) ? 'selected' : '' }}>
            {{ $branch->name }}
          </option>
          @endforeach
        </select>
        <small style="display: block; color: var(--text-muted); font-size: 0.78rem; margin-top: 6px;">
          The managers of the selected branch will receive a notification to fulfill this order.
        </small>
      </div>

      <div class="assign-modal-footer">
        <button type="button" class="btn-smart btn-red" onclick="closeAssignBranchModal()">
          Cancel
        </button>
        <button type="submit" class="btn-smart btn-green">
          <i class="fas fa-check-double"></i> Confirm & Approve
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  .assign-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 99999;
    display: none;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.25s ease;
  }

  .assign-modal-overlay.show {
    display: flex;
    opacity: 1;
  }

  .assign-modal-box {
    background: var(--section-bg, #ffffff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 16px;
    width: 90%;
    max-width: 480px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
    padding: 24px;
    position: relative;
    transform: translateY(-20px) scale(0.96);
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  .assign-modal-overlay.show .assign-modal-box {
    transform: translateY(0) scale(1);
  }

  .assign-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
  }

  .assign-modal-header h4 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-main, #1e293b);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .assign-modal-close-btn {
    background: transparent;
    border: none;
    font-size: 18px;
    color: var(--text-muted, #94a3b8);
    cursor: pointer;
    line-height: 1;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s;
  }

  .assign-modal-close-btn:hover {
    color: var(--text-main, #1e293b);
    background: var(--primary-soft, #f1f5f9);
  }

  .assign-order-info {
    background: var(--primary-soft, #f8fafc);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 18px;
    font-size: 0.85rem;
  }

  .assign-order-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
    color: var(--text-muted);
  }

  .assign-order-info-row strong {
    color: var(--text-main);
  }

  .assign-form-group {
    margin-bottom: 20px;
  }

  .assign-form-group label {
    display: block;
    font-weight: 600;
    font-size: 0.88rem;
    color: var(--text-main);
    margin-bottom: 8px;
  }

  .assign-branch-select {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    border: 1.5px solid var(--border-color, #cbd5e1);
    background: var(--section-bg, #ffffff);
    color: var(--text-main);
    font-size: 0.92rem;
    font-weight: 500;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }

  .assign-branch-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
  }

  .assign-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }
</style>

<script>
  function openAssignBranchModal() {
    var modal = document.getElementById('assignBranchModal');
    if (modal) {
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeAssignBranchModal() {
    var modal = document.getElementById('assignBranchModal');
    if (modal) {
      modal.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  function handleAssignModalBackdrop(event) {
    if (event.target.id === 'assignBranchModal') {
      closeAssignBranchModal();
    }
  }
</script>
@endif

<div style="text-align: center; margin-top: 20px;">
  <a href="{{ route('admin.order.index') }}" style="color: var(--text-muted); text-decoration: none;">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>
@endsection