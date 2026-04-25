@extends('layouts.managerlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>My Orders</h2>
    <p>Manage all orders from your branch SRs</p>
    @include('components.alert')
  </div>
  <div style="margin: 15px 0;">
    <input type="text" id="search" class="input-form" placeholder="Search by Order ID or Customer name...">
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Reference</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        @include('pages.manager.order.table')
      </tbody>
    </table>
  </div>
  <div class="manage-mobile-cards" id="mobileTable">
    @include('pages.manager.order.mtable')
  </div>


</div>
<div class="mt-3">
  {{ $orders->links() }} </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('search').addEventListener('keyup', function () {
    let query = this.value;

    fetch(`{{ route('manager.order.index') }}?search=${query}`, {
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