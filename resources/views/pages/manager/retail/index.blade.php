@extends('layouts.managerlayout')

@section('content')
<style>
  .retail-header-card {
    background: var(--section-bg);
    border-radius: 14px;
    padding: 20px 22px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0, 0, 0, .05);
    margin-bottom: 22px;
  }

  .retail-table-card {
    background: var(--section-bg);
    border-radius: 14px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0, 0, 0, .05);
    overflow: hidden;
  }

  .r-table {
    width: 100%;
    border-collapse: collapse;
  }

  .r-table thead {
    background: var(--primary-soft);
  }

  .r-table thead th {
    padding: 13px 16px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: var(--primary);
    font-family: 'Inter', sans-serif;
    white-space: nowrap;
    border-bottom: 1px solid var(--border-color);
  }

  .r-table tbody tr {
    border-bottom: 1px solid var(--border-color);
    transition: background .15s ease;
  }

  .r-table tbody tr:hover {
    background: var(--primary-soft);
  }

  .r-table tbody td {
    padding: 13px 16px;
    font-size: 13px;
    color: var(--text-main);
    font-family: 'Inter', sans-serif;
    vertical-align: middle;
  }

  .r-table tbody tr:last-child {
    border-bottom: none;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
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
    font-size: 11px;
    font-weight: 600;
    padding: 5px 11px;
    border-radius: 8px;
    text-decoration: none;
    transition: all .2s ease;
    border: none;
    cursor: pointer;
  }

  .btn-view {
    background: rgba(102, 126, 234, .1);
    color: #667eea;
  }

  .btn-edit {
    background: rgba(245, 158, 11, .1);
    color: #f59e0b;
  }

  .btn-del {
    background: rgba(239, 68, 68, .1);
    color: #ef4444;
  }

  .btn-invoice {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
  }

  .action-btn:hover {
    opacity: .75;
  }

  .search-bar {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
  }

  .search-bar input {
    background: var(--section-bg);
    color: var(--text-main);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    min-width: 220px;
  }

  .search-bar input:focus {
    outline: none;
    border-color: var(--primary);
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
    font-family: 'Inter', sans-serif;
  }

  .empty-state i {
    font-size: 40px;
    margin-bottom: 14px;
    display: block;
    opacity: .35;
  }

  /* Mobile card view */
  .retail-mobile-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
  }
</style>

<div class="retail-header-card d-flex justify-content-between align-items-center flex-wrap gap-3 animate__animated animate__fadeIn">
  <div>
    <h2 class="m-0" style="font-size:20px; font-family:'Cinzel',serif; color:var(--primary); font-weight:700;">
      <i class="fas fa-store me-2"></i>Retail Sales
    </h2>
    <p class="m-0 mt-1" style="font-size:12px; color:var(--text-muted); font-family:'Inter',sans-serif;">
      Direct orders created by you (auto-approved)
    </p>
  </div>
  <a href="{{ route('manager.retail.create') }}" class="action-btn btn-view px-4 py-2" style="font-size:13px;">
    <i class="fas fa-plus-circle"></i> New Retail Order
  </a>
</div>

{{-- Search and Filter Controls --}}
<form method="GET" action="{{ route('manager.retail.index') }}" class="mb-4 search-bar animate__animated animate__fadeIn" style="animation-delay: 0.1s; background: var(--section-bg); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
  
  {{-- Search Input --}}
  <div style="flex: 1; min-width: 200px;">
    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
    <div style="position: relative;">
      <input type="text" 
             name="search" 
             class="form-control" 
             placeholder="Order # or Customer..." 
             value="{{ request('search') }}"
             style="background: var(--section-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px; padding-left: 36px; height: 42px;">
      <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
    </div>
  </div>

  {{-- Status Filter --}}
  <div style="min-width: 160px;">
    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Filter Status</label>
    <select name="status" class="form-select" style="background: var(--section-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px; height: 42px;">
      <option value="">-- All Statuses --</option>
      <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
      <option value="pending_manager" {{ request('status') == 'pending_manager' ? 'selected' : '' }}>Pending Manager</option>
      <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Complete</option>
      <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
      <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
    </select>
  </div>

  {{-- From Date --}}
  <div style="min-width: 120px;">
    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
    <input type="date" 
           name="from_date" 
           class="form-control" 
           value="{{ request('from_date') }}"
           style="min-width: 110px; background: var(--section-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px; height: 42px;">
  </div>

  {{-- To Date --}}
  <div style="min-width: 120px;">
    <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
    <input type="date" 
           name="to_date" 
           class="form-control" 
           value="{{ request('to_date') }}"
           style="min-width: 110px; background: var(--section-bg); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 8px; height: 42px;">
  </div>

  {{-- Action Buttons --}}
  <div style="display: flex; gap: 8px;">
    <button type="submit" class="action-btn btn-view" style="height: 42px; padding: 0 1.2rem; border-radius: 8px;">
      <i class="fas fa-filter me-1"></i> Filter
    </button>
    @if(request('search') || request('status') || request('from_date') || request('to_date'))
    <a href="{{ route('manager.retail.index') }}" class="action-btn btn-del" style="height: 42px; padding: 0 1rem; border-radius: 8px; display: inline-flex; align-items: center; text-decoration: none;">
      <i class="fas fa-undo me-1"></i> Reset
    </a>
    @endif
  </div>

</form>

{{-- Desktop Table --}}
<div class="retail-table-card d-none d-md-block animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
  <table class="r-table">
    <thead>
      <tr>
        <th>#Order</th>
        <th>Customer</th>
        <th>Net Total</th>
        <th>Deduction %</th>
        <th>Status</th>
        <th>Date</th>
        <th style="text-align:right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
      <tr>
        <td><span style="font-weight:700; color:var(--primary);">BRS{{ $order->id }}</span></td>
        <td>{{ $order->customer->shop_name ?? '—' }}</td>
        <td><strong>৳ {{ number_format($order->net_total, 2) }}</strong></td>
        <td>{{ $order->applied_deduction_percent ?? 0 }}%</td>
        <td>
          <span class="status-pill pill-{{ $order->status }}">
            <i class="fas fa-circle" style="font-size:6px;"></i>
            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
          </span>
        </td>
        <td style="color:var(--text-muted);">{{ $order->created_at->format('d M Y') }}</td>
        <td style="text-align:right;">
          <a href="{{ route('manager.order.view_retail_invoice', $order->id) }}" class="action-btn btn-invoice">
            <i class="fas fa-print"></i> Invoice
          </a>
          <a href="{{ route('manager.retail.show', $order->id) }}" class="action-btn btn-view">
            <i class="fas fa-eye"></i> View
          </a>
          @if(!in_array($order->status, ['complete','delivered']))
          <a href="{{ route('manager.retail.edit', $order->id) }}" class="action-btn btn-edit">
            <i class="fas fa-pen"></i> Edit
          </a>
          <form method="POST" action="{{ route('manager.retail.destroy', $order->id) }}" style="display:inline;" onsubmit="return confirm('Delete BRS{{ $order->id }}?')">
            @csrf @method('DELETE')
            <button type="submit" class="action-btn btn-del">
              <i class="fas fa-trash"></i> Del
            </button>
          </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7">
          <div class="empty-state">
            <i class="fas fa-store-slash"></i>
            <p>No retail orders found.</p>
            <a href="{{ route('manager.retail.create') }}" class="action-btn btn-view px-4">
              <i class="fas fa-plus-circle me-1"></i> Create First Order
            </a>
          </div>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Mobile Cards --}}
<div class="d-md-none animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
  @forelse($orders as $order)
  <div class="retail-mobile-card">
    <div class="d-flex justify-content-between align-items-start mb-2">
      <strong style="color:var(--primary);">BRS{{ $order->id }}</strong>
      <span class="status-pill pill-{{ $order->status }}">
        <i class="fas fa-circle" style="font-size:6px;"></i>
        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
      </span>
    </div>
    <p class="mb-1" style="font-size:13px;">{{ $order->customer->shop_name ?? '—' }}</p>
    <p class="mb-2" style="font-size:14px; font-weight:700; color:var(--primary);">
      ৳ {{ number_format($order->net_total, 2) }}
    </p>
    <p class="mb-2" style="font-size:11px; color:var(--text-muted);">
      <i class="fas fa-calendar-alt me-1"></i>{{ $order->created_at->format('d M Y') }}
      &nbsp;|&nbsp; Deduction: {{ $order->applied_deduction_percent ?? 0 }}%
    </p>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('manager.order.view_retail_invoice', $order->id) }}" class="action-btn btn-invoice">
        <i class="fas fa-print"></i> Invoice
      </a>
      <a href="{{ route('manager.retail.show', $order->id) }}" class="action-btn btn-view">
        <i class="fas fa-eye"></i> View
      </a>
      @if(!in_array($order->status, ['complete','delivered']))
      <a href="{{ route('manager.retail.edit', $order->id) }}" class="action-btn btn-edit">
        <i class="fas fa-pen"></i> Edit
      </a>
      <form method="POST" action="{{ route('manager.retail.destroy', $order->id) }}" onsubmit="return confirm('Delete BRS{{ $order->id }}?')">
        @csrf @method('DELETE')
        <button type="submit" class="action-btn btn-del"><i class="fas fa-trash"></i> Del</button>
      </form>
      @endif
    </div>
  </div>
  @empty
  <div class="empty-state">
    <i class="fas fa-store-slash"></i>
    <p>No retail orders found.</p>
  </div>
  @endforelse
</div>

{{-- Pagination --}}
@if($orders->hasPages())
<div class="mt-3 d-flex justify-content-center">
  {{ $orders->links() }}
</div>
@endif
@endsection
