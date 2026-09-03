@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">{{ $title }}</h2>
      <p class="text-muted mb-0">Detailed inventory report</p>
    </div>
    <a href="{{ route('admin.stocks.all') }}" class="btn"
      style="background: var(--primary-soft); color: var(--primary); border: none; padding: 8px 15px; border-radius: 8px;">
      <i class="fa-solid fa-arrow-left"></i> Back
    </a>
  </div>

  {{-- Smart Filter Bar --}}
  <div style="margin: 15px 0; background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
    <form method="GET" action="{{ route('admin.stocks.specific', $branch_id ?? '') }}" id="filterForm">
      <div class="row g-2 align-items-end">

        {{-- Search --}}
        <div class="col-12 col-md-5 col-lg-5">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
          <div style="position: relative;">
            <input type="text" name="search" id="searchInput" class="input-form"
              placeholder="Search by Product or Supplier..."
              value="{{ request('search') }}"
              style="margin-bottom: 0; padding-left: 35px; height: 42px;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
          </div>
        </div>

        {{-- Status Filter --}}
        <div class="col-6 col-md-2 col-lg-2">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Status</label>
          <select name="status" id="statusFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
            <option value="">-- All --</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
          </select>
        </div>

        {{-- Branch Filter (only when showing company total) --}}
        @if(!$branch_id)
        <div class="col-6 col-md-3 col-lg-3">
          <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Branch</label>
          <select name="branch_id" id="branchFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
            <option value="">-- All Branches --</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
        @endif

        {{-- Filter & Reset --}}
        <div class="col-6 col-md-2 col-lg-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-50" style="height: 42px;">
            <i class="fas fa-filter"></i>
          </button>
          <a href="{{ route('admin.stocks.specific', $branch_id ?? '') }}" class="btn btn-outline-secondary w-50" style="height: 42px; display: inline-flex; align-items: center; justify-content: center;" title="Reset">
            <i class="fas fa-undo"></i>
          </a>
        </div>
      </div>
    </form>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Product Name</th>
          <th>Supplier</th>
          <th>{{ $branch_id ? 'Current Qty' : 'Total System Qty' }}</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($stocks as $stock)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td><strong>{{ $stock->product->name }}</strong></td>
          <td>{{ $stock->product->supplier->company_name ?? 'N/A' }}</td>
          <td>{{ $stock->quantity }} Pcs</td>
          <td>
            @if($stock->quantity <= $stock->product->stock_alert)
              <span style="background: rgba(220,53,69,0.1); color: #dc3545; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">⚠ Low Stock</span>
            @else
              <span style="background: rgba(22,163,74,0.1); color: #16a34a; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">✓ Available</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted">No stock data found for current filters.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @forelse($stocks as $stock)
    <div class="manage-card" style="border: 1px solid var(--border-color); margin-bottom: 10px;">
      <div class="card-body">
        <div><span>Product</span>
          <p>{{ $stock->product->name }}</p>
        </div>
        <div><span>Qty</span>
          <p>{{ $stock->quantity }} Pcs</p>
        </div>
        <div><span>Status</span>
          <p style="color: {{ $stock->quantity <= $stock->product->stock_alert ? '#ef4444' : '#16a34a' }}">
            ● {{ $stock->quantity <= $stock->product->stock_alert ? 'Low Stock' : 'In Stock' }}
          </p>
        </div>
      </div>
    </div>
    @empty
    <p class="text-center text-muted">No stock data found.</p>
    @endforelse
  </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-submit on filter change
    ['statusFilter', 'branchFilter'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', function () {
            document.getElementById('filterForm').submit();
        });
    });

    // Debounced search submit
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let timer;
        searchInput.addEventListener('keyup', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }
});
</script>
@endpush

@endsection