@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      @include('components.alert')
      <h2>All Users</h2>
      <p class="text-muted mb-0">Manage all registered user accounts</p>
    </div>
    <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
      <i class="fas fa-users me-1"></i> Total Users: <span id="totalUsersCount">{{ $users->total() }}</span>
    </div>
  </div>

  {{-- Smart Filter Bar --}}
  <div style="margin: 15px 0; background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
    <div class="row g-2 align-items-end">
      {{-- Search Bar --}}
      <div class="col-12 col-md-5 col-lg-5">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
        <div style="position: relative;">
          <input type="text" id="search" class="input-form" placeholder="Search by Full Name or Username..." value="{{ request('search') }}" style="margin-bottom: 0; padding-left: 35px; height: 42px;">
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
      </div>

      {{-- Role Filter --}}
      <div class="col-6 col-md-3 col-lg-3">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Role</label>
        <select id="roleFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
          <option value="">-- All Roles --</option>
          <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
          <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
          <option value="sr" {{ request('role') == 'sr' ? 'selected' : '' }}>SR</option>
          <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
        </select>
      </div>

      {{-- Branch Filter --}}
      <div class="col-6 col-md-3 col-lg-3">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Branch</label>
        <select id="branchFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
          <option value="">-- All Branches --</option>
          @foreach($branches as $b)
          <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Reset Button --}}
      <div class="col-12 col-md-1 col-lg-1">
        <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100" title="Reset Filters" style="margin-bottom: 0; height: 42px; display: inline-flex; align-items: center; justify-content: center;">
          <i class="fas fa-undo"></i>
        </button>
      </div>
    </div>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Role</th>
          <th>Branch</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        @include('pages.admin.users.table')
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards" id="mobileTable">
    @include('pages.admin.users.mtable')
  </div>

</div>

<div class="d-flex justify-content-center mt-3">
  {{ $users->links() }}
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput  = document.getElementById('search');
    const roleFilter   = document.getElementById('roleFilter');
    const branchFilter = document.getElementById('branchFilter');
    const resetBtn     = document.getElementById('resetBtn');

    function fetchFilteredUsers() {
        const query  = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
        const role   = encodeURIComponent(roleFilter ? roleFilter.value : '');
        const branch = encodeURIComponent(branchFilter ? branchFilter.value : '');

        const url = `{{ route('admin.index.users') }}?search=${query}&role=${role}&branch_id=${branch}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('desktopTable').innerHTML = data.table;
            document.getElementById('mobileTable').innerHTML = data.mobile;
            if (document.getElementById('totalUsersCount') && data.total !== undefined) {
                document.getElementById('totalUsersCount').innerText = data.total;
            }
        })
        .catch(err => console.error('Filter fetch error:', err));
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchFilteredUsers, 350);
        });
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', fetchFilteredUsers);
    }

    if (branchFilter) {
        branchFilter.addEventListener('change', fetchFilteredUsers);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (branchFilter) branchFilter.value = '';
            fetchFilteredUsers();
        });
    }
});
</script>
@endpush