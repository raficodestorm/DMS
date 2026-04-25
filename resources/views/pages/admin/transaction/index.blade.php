@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>All Transactions</h2>
    <p>View all payment and purchase records of your customers</p>
    @include('components.alert')
  </div>
  <div style="margin: 15px 0;">
    <input type="text" id="search" class="input-form" placeholder="Search by Trasaction ID or Customer name...">
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Transaction ID</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody class="desktop-table" id="desktopTable">
        @include('pages.admin.transaction.table')
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    @include('pages.admin.transaction.mtable')
  </div>

</div>

<div class="mt-3">
  {{ $payments->links() }}
</div>

@endsection

@push('scripts')
<script>
  document.getElementById('search').addEventListener('keyup', function () {
    let query = this.value;

    fetch(`{{ route('admin.payments.index') }}?search=${query}`, {
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