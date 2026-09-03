@forelse($customers as $customer)
<div class="manage-card">

  <div class="card-body">
    <div><span>S. No</span>
      <p>{{ $customers->firstItem() ? $customers->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Full name</span>
      <p>{{ $customer->fullname }}</p>
    </div>
    <div><span>Username</span>
      <p>{{ $customer->username }}</p>
    </div>
    <div><span>Branch</span>
      <p>{{ $customer->branch->name ?? "-" }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('manager.users.show', $customer) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>

</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse
