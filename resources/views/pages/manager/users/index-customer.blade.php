@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <h2>All Customer Accounts</h2>
    <p>Manage all registered Customer Accounts</p>
  </div>

  <div style="margin: 15px 0;">
    <input type="text" id="search" class="input-form" placeholder="Search by Full Name, Username or Customer ID..." value="{{ request('search') }}">
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Branch</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        @include('pages.manager.users.customer-table')
      </tbody>
    </table>
  </div>

  <div class="manage-mobile-cards" id="mobileTable">
    @include('pages.manager.users.customer-mtable')
  </div>

</div>

<div class="d-flex justify-content-center mt-3">
  {{ $customers->links() }}
</div>

@endsection

@push('scripts')
<script>
  document.getElementById('search').addEventListener('keyup', function () {
    let query = this.value;

    fetch(`{{ route('manager.index.customers') }}?search=${query}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
      document.getElementById('desktopTable').innerHTML = data.table;
      document.getElementById('mobileTable').innerHTML = data.mobile;
    });
  });
</script>
@endpush