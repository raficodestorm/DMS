@forelse($stocks as $stock)
<div class="manage-card" style="margin-bottom: 10px; border: 1px solid #eee;">
  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $stocks->firstItem() ? $stocks->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Product</span>
      <p><strong>{{ $stock->product->name ?? '-' }}</strong></p>
    </div>
    <div><span>Supplier</span>
      <p>{{ $stock->product->supplier->company_name ?? '-' }}</p>
    </div>
    <div><span>Qty</span>
      <p>{{ $stock->quantity }}</p>
    </div>
    <div><span>Status</span>
      <p>
        @if($stock->product && $stock->quantity <= $stock->product->stock_alert)
          <span style="color:#dc3545; font-weight: 600;">Low Stock</span>
        @else
          <span style="color:#16a34a; font-weight: 600;">Available</span>
        @endif
      </p>
    </div>
  </div>
</div>
@empty
<p class="text-center text-muted">No stock items found.</p>
@endforelse
