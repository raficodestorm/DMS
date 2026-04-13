@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>All Customers</h2>
        <p>Manage all registered Customers</p>
    </div>
    <div style="margin: 15px 0;">
        <input type="text" id="search" class="input-form" placeholder="Search by Shop-name or ID...">
    </div>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Customer ID</th>
                    <th>Shop Name</th>
                    <th>Manager</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="customerTable">
                @include('pages.admin.customer.table')
            </tbody>
        </table>
    </div>
    <div class="manage-mobile-cards" id="customerMobile">
        @include('pages.admin.customer.mtable')
    </div>


</div>

@endsection

@push('scripts')
<script>
    document.getElementById('search').addEventListener('keyup', function () {
    let query = this.value;

    fetch(`{{ route('admin.customers.index') }}?search=${query}`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
.then(res => res.json())
.then(data => {
    document.getElementById('customerTable').innerHTML = data.table;
    document.getElementById('customerMobile').innerHTML = data.mobile;
});
});
</script>
@endpush