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
    <div><span>Manager</span>
      <p>{{ $customer->manager }}</p>
    </div>
    <div><span>Due</span>
      <p>{{ number_format($customer->due ?? 0, 2) }} TK</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('customers.show', $customer) }}" class="icon-btn view-icon" title="View">
      <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('customers.edit', $customer) }}" class="icon-btn edit-icon" title="Edit">
      <i class="fas fa-edit"></i>
    </a>
  </div>

</div>
@empty
<p class="text-center text-muted py-4">No customer records found.</p>
@endforelse