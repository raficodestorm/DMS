@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div>
      <h2>{{ $title }}</h2>
      <p>Detailed inventory report</p>
    </div>
    <a href="{{ route('admin.stocks.all') }}" class="btn"
      style="background: var(--primary-soft); color: var(--primary); border: none; padding: 8px 15px; border-radius: 8px;">
      <i class="fa-solid fa-arrow-left"></i> Back
    </a>
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
          <td>{{ $stock->product->supplier->company_name }}</td>
          <td>{{ $stock->quantity }} Pcs</td>
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
          <td colspan="5" class="text-center">No data found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards">
    @foreach($stocks as $stock)
    <div class="manage-card" style="border: 1px solid var(--border-color); margin-bottom: 10px;">
      <div class="card-body">
        <div><span>Product</span>
          <p>{{ $stock->product->name }}</p>
        </div>
        <div><span>Qty</span>
          <p>{{ $stock->quantity }}</p>
        </div>
        <div><span>Status</span>
          <p style="color: {{ $stock->quantity <= $stock->product->stock_alert ? '#ef4444' : '#16a34a' }}">
            ● {{ $stock->quantity <= $stock->product->stock_alert ? 'Low Stock' : 'In Stock' }}
          </p>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection