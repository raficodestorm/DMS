@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>Current Stock</h2>
    <p>Monitor your branch inventory</p>
    @include('components.alert')
  </div>

  {{-- Search and Filter Controls --}}
  <form method="GET" action="{{ route('manager.stock.index') }}" class="mb-4" id="stockFilterForm" onsubmit="return false;">
    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; justify-content: space-between; margin-bottom: 20px;">
      
      {{-- Search Input (by Product Name or Supplier Name) --}}
      <div style="flex: 1; min-width: 240px; position: relative;">
        <input type="text" 
               name="search" 
               id="stockSearchInput"
               class="input-form" 
               placeholder="Search by product or supplier name..." 
               value="{{ request('search') }}"
               style="margin-bottom: 0; padding-left: 38px;">
        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
      </div>

      {{-- Status Filter Dropdown --}}
      <div style="min-width: 180px;">
        <select name="status" id="stockStatusFilter" class="input-form" style="margin-bottom: 0;">
          <option value="">-- All Statuses --</option>
          <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
          <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
        </select>
      </div>

      {{-- Reset Button --}}
      <div id="resetBtnWrapper" style="display: {{ (request('search') || request('status')) ? 'block' : 'none' }};">
        <button type="button" id="resetStockFilterBtn" class="btn-submit" style="padding: 0.6rem 1rem; font-size: 0.85rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; background: #6c757d; color: #fff; border-radius: 8px;">
          <i class="fas fa-undo"></i> Reset
        </button>
      </div>

    </div>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Product Name</th>
          <th>Supplier</th>
          <th>Unit Price</th>
          <th>Quantity</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody class="desktop-table">
        @forelse($stocks as $stock)
        <tr class="stock-item-row">
          <td>{{ $loop->iteration }}</td>
          <td><strong>{{ $stock->product->name }}</strong></td>
          <td>{{ $stock->product->supplier->company_name }}</td>
          <td>{{ number_format($stock->product->price, 2) }} TK</td>
          <td>{{ $stock->quantity }}</td>
          <td>
            @if($stock->quantity <= $stock->product->stock_alert)
              <span
                style="background: var(--danger); color: #dc3545; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Low
                Stock</span>
              @else
              <span
                style="background: var(--success); color: #16a34a; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">Available</span>
              @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted">No stock data available for your branch.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Mobile Responsive Cards --}}
  <div class="manage-mobile-cards">
    @forelse($stocks as $stock)
    <div class="manage-card stock-item-card" style="margin-bottom: 10px; border: 1px solid #eee;">
      <div class="card-body">
        <div><span>Product</span>
          <p><strong>{{ $stock->product->name }}</strong></p>
        </div>
        <div><span>Supplier</span>
          <p>{{ $stock->product->supplier->company_name }}</p>
        </div>
        <div><span>Qty</span>
          <p>{{ $stock->quantity }}</p>
        </div>
        <div><span>Status</span>
          <p>
            @if($stock->quantity <= $stock->product->stock_alert)
              <span style="color:#dc3545; font-weight: 600;">● Low Stock (Below {{ $stock->product->stock_alert
                }})</span>
              @else
              <span style="color:#28a745; font-weight: 600;">Available</span>
              @endif
          </p>
        </div>
      </div>
    </div>
    @empty
    <p class="text-center text-muted">No stock data found.</p>
    @endforelse
  </div>

</div>
@endsection

@push('scripts')
<script type="module">
  $(document).ready(function () {
    function filterStockTable() {
      const searchVal = $('#stockSearchInput').val().toLowerCase().trim();
      const statusVal = $('#stockStatusFilter').val();

      // Show/hide reset button dynamically
      if (searchVal || statusVal) {
        $('#resetBtnWrapper').show();
      } else {
        $('#resetBtnWrapper').hide();
      }

      // Filter Desktop Table Rows
      $('.stock-item-row').each(function () {
        const $row = $(this);
        const productName  = $row.find('td:nth-child(2)').text().toLowerCase();
        const supplierName = $row.find('td:nth-child(3)').text().toLowerCase();
        const statusText   = $row.find('td:nth-child(6)').text().toLowerCase();
        const isLowStock   = statusText.includes('low stock');

        const matchesSearch = !searchVal || productName.includes(searchVal) || supplierName.includes(searchVal);
        let matchesStatus = true;

        if (statusVal === 'low_stock') {
          matchesStatus = isLowStock;
        } else if (statusVal === 'available') {
          matchesStatus = !isLowStock;
        }

        if (matchesSearch && matchesStatus) {
          $row.show();
        } else {
          $row.hide();
        }
      });

      // Filter Mobile Cards
      $('.stock-item-card').each(function () {
        const $card = $(this);
        const productName  = $card.find('.card-body div:nth-child(1) p').text().toLowerCase();
        const supplierName = $card.find('.card-body div:nth-child(2) p').text().toLowerCase();
        const statusText   = $card.find('.card-body div:nth-child(4) p').text().toLowerCase();
        const isLowStock   = statusText.includes('low stock');

        const matchesSearch = !searchVal || productName.includes(searchVal) || supplierName.includes(searchVal);
        let matchesStatus = true;

        if (statusVal === 'low_stock') {
          matchesStatus = isLowStock;
        } else if (statusVal === 'available') {
          matchesStatus = !isLowStock;
        }

        if (matchesSearch && matchesStatus) {
          $card.show();
        } else {
          $card.hide();
        }
      });
    }

    // Initial filter execution
    filterStockTable();

    // Input & Change listeners
    $('#stockSearchInput').on('keyup input', filterStockTable);
    $('#stockStatusFilter').on('change', filterStockTable);

    // Instant Reset
    $('#resetStockFilterBtn').on('click', function () {
      $('#stockSearchInput').val('');
      $('#stockStatusFilter').val('');
      filterStockTable();
      if (window.history.pushState) {
        window.history.pushState(null, '', '{{ route("manager.stock.index") }}');
      }
    });
  });
</script>
@endpush