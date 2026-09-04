@forelse($employees as $employee)
<div class="manage-card">

  <div class="card-body">
    <div><span>S.No</span>
      <p>{{ $employees->firstItem() ? $employees->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Employee ID</span>
      <p>BRE100{{ $employee->id }}</p>
    </div>
    <div><span>Name</span>
      <p>{{ $employee->name }}</p>
    </div>
    <div><span>Rank</span>
      <p>{{ $employee->rank }}</p>
    </div>
    <div><span>Phone</span>
      <p>{{ $employee->phone }}</p>
    </div>
    <div><span>Branch</span>
      <p>{{ $employee->branch->name ?? 'N/A' }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('admin.employees.show', $employee) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>

</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse