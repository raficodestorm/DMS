@forelse($orders as $order)
<tr>
  <td scope="row">{{ $orders->firstItem() ? $orders->firstItem() + $loop->index : $loop->iteration}}</td>
  <td>{{ $order->order_id ?? ('BRS' . $order->id) }}</td>
  <td>{{ $order->branch->name ?? "N/A" }}</td>
  <td>{{ number_format($order->net_total, 2) }} TK</td>
  
  <td>
    @if($order->order_type == "field_order")
    <span class="emerald-type-badge">Field Order</span>
    @elseif($order->order_type == 'retail')
    <span class="pink-type-badge">Retail</span>
    @elseif($order->order_type == 'online')
    <span class="purple-type-badge">Online</span>
    @else
    <span class="status-undefined-badge">Undefined</span>
    @endif
  </td>

  <td>
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
      {{-- Locked: cannot change --}}
      <span class="{{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
    @else
      {{-- Changeable: show badge + arrow dropdown --}}
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
  </td>

  <td >
    @if($order->payment_status == "unpaid")
    <span class="orange-type-badge ">Unpaid</span>
    @elseif($order->payment_status == "partial")
    <span class="purple-type-badge">Partial</span>
    @elseif($order->payment_status == 'paid')
    <span class="emerald-type-badge">Paid</span>
    @else
    <span class="status-undefined-badge">Undefined</span>
    @endif
  </td>

  <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>

  <td class="action-icons">
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
  </td>
</tr>
@empty
<tr>
  <td colspan="9" class="text-center text-muted">No orders found.</td>
</tr>
@endforelse