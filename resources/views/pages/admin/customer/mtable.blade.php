@forelse($customers as $customer)
<div class="manage-card">

  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $customers->firstItem() ? $customers->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Customer ID</span>
      <p>BRC200{{ $customer->id }}</p>
    </div>
    <div><span>Shop Name</span>
      <p>{{ $customer->shop_name }}</p>
    </div>
    <div><span>Customer Type</span>
      <p>
        @if($customer->customer_type === 'retail')
          <span class="purple-type-badge">Retail</span>
        @else
          <span class="emerald-type-badge">Wholesale</span>
        @endif
      </p>
    </div>
    <div><span>Address</span>
      <p>{{ $customer->address }}</p>
    </div>
    <div><span>Area Branch</span>
      <p>{{ $customer->branch->name ?? 'N/A' }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('admin.customers.show', $customer) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>

</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse