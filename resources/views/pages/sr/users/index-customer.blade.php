@extends('layouts.srlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>All Customer Accounts</h2>
    <p>Manage all registered Customer Accounts</p>
    @include('components.alert')
  </div>

  <div style="margin: 15px 0;">
    <input type="text" id="search" class="input-form" placeholder="Search by Full Name, Username or Customer ID (e.g. BRC2001)...">
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Customer ID</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody class="desktop-table" id="desktopTable">
        @include('pages.sr.users.table')
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    @include('pages.sr.users.mtable')
  </div>

</div>

<div class="mt-3">
  {{ $customers->links() }}
</div>

@endsection

@push('scripts')
<script>
  document.getElementById('search').addEventListener('keyup', function () {
    let query = this.value;

    fetch(`{{ route('sr.index.customers') }}?search=${query}`, {
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