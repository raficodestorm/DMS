@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>Current Stock</h2>
    <p>Monitor your branch inventory</p>
    @include('components.alert')
  </div>

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
        <tr>
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
    <div class="manage-card" style="margin-bottom: 10px; border: 1px solid #eee;">
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