@forelse($srs as $sr)
<div class="manage-card">

  <div class="card-body">
    <div><span>S. No</span>
      <p>{{ $srs->firstItem() ? $srs->firstItem() + $loop->index : $loop->iteration }}</p>
    </div>
    <div><span>Full name</span>
      <p>{{ $sr->fullname }}</p>
    </div>
    <div><span>Username</span>
      <p>{{ $sr->username }}</p>
    </div>
    <div><span>Branch</span>
      <p>{{ $sr->branch->name ?? "-" }}</p>
    </div>
  </div>

  <div class="card-actions">
    <a href="{{ route('manager.users.show', $sr) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </div>

</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse
