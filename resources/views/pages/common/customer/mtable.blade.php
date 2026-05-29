@forelse($customers as $customer)
<div class="manage-card">

  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $loop->iteration }}</p>
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
    <div><span>Address</span>
      <p>{{ $customer->address }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('customers.show', $customer) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>

  </div>

</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse