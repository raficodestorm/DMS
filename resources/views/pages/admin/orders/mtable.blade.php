@forelse($orders as $order)
<div class="manage-card">
  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Order ID</span>
      <p>{{ $order->order_id ?? ('BRS' . $order->id) }}</p>
    </div>
    <div><span>Branch</span>
      <p>{{ $order->branch->name ?? "N/A" }}</p>
    </div>

    <div><span>Amount</span>
      <p>{{ number_format($order->net_total, 2) }} TK</p>
    </div>

    <div><span>Order Type</span>
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
    
    <div><span>Status</span>
      <p>
        @php
          $isLocked = in_array($order->status, ['complete', 'delivered']);
          $statusBadge = match($order->status) {
            'pending_sr'      => ['class' => 'status-pending-badge',   'label' => 'Pending..SR..'],
            'pending_manager' => ['class' => 'status-pmanager-badge',  'label' => 'Pending..Manager..'],
            'rejected'        => ['class' => 'status-rejected-badge',  'label' => 'Rejected'],
            'complete'        => ['class' => 'status-complete-badge',  'label' => 'Complete'],
            'delivered'       => ['class' => 'status-delivered-badge', 'label' => 'Delivered'],
            'approved'        => ['class' => 'status-approved-badge',  'label' => 'Approved'],
            default           => ['class' => 'status-undefined-badge', 'label' => 'Undefined'],
          };
        @endphp
        @if($isLocked)
          <span class="{{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
        @else
          <div class="os-wrap" data-order-id="{{ $order->id }}">
            <span class="{{ $statusBadge['class'] }} os-badge">{{ $statusBadge['label'] }}</span>
            <button class="os-arrow-btn" title="Change Status" type="button">
              <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="os-dropdown">
              @foreach(['pending_sr' => 'Pending SR', 'pending_manager' => 'Pending Manager', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $lbl)
                <div class="os-option {{ $order->status === $val ? 'active' : '' }}" data-value="{{ $val }}">
                  {{ $lbl }}
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </p>
    </div>
    <div><span>Pay Status</span>
      <p>
        @if($order->payment_status == "unpaid")
        <span class="orange-type-badge">Unpaid</span>
        @elseif($order->payment_status == 'partial')
        <span class="purple-type-badge">Partial</span>
        @elseif($order->payment_status == 'paid')
        <span class="emerald-type-badge">Paid</span>
        @else
        <span class="status-undefined-badge">Undefined</span>
        @endif
      </p>
    </div>
    <div><span>Date & Time</span>
      <p>{{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('admin.order.show', $order->id) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
    @php
    $invoicePrefix = match (auth()->user()->role) {
        'admin', 'editor' => 'admin',
        'manager'         => 'manager',
        'sr'              => 'sr',
        'dso'             => 'dso',
        'customer'        => 'customer',
        default           => null,
    };
@endphp

@if(
    $invoicePrefix &&
    in_array($order->status, ['complete', 'delivered'], true) &&
    ($invoiceRoute = $order->invoiceRouteName($invoicePrefix))
)
    <a href="{{ route($invoiceRoute, $order->id) }}"
       class="icon-btn slip-icon"
       title="View Purchase Invoice">
        <i class="fa-solid fa-file-invoice"></i>
    </a>
@endif
  </div>
</div>
@empty
<p class="text-center text-muted">No orders found.</p>
@endforelse