@forelse($users as $user)
<div class="manage-card">

  <div class="card-body">
    <div><span>S. No</span>
      <p>{{ $users->firstItem() ? $users->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Full name</span>
      <p>{{ $user->fullname ?? "-" }}</p>
    </div>
    <div><span>Username</span>
      <p>{{ $user->username ?? "-" }}</p>
    </div>
    <div><span>Role</span>
      <p><span class="badge bg-primary text-uppercase text-white">{{ $user->role ?? "-" }}</span></p>
    </div>
    <div><span>Branch</span>
      <p>{{ $user->branch->name ?? "-" }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('admin.users.show', $user) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>

</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse
