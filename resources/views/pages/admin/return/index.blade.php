@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>Return Dashboard</h2>
        <p>Manage all product returns across branches</p>
        @include('components.alert')
    </div>
    
    <div style="margin: 15px 0; display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="text" id="search" class="input-form" placeholder="Search by BRET ID or Customer name..." style="flex: 1; min-width: 200px;">
        <select id="status-filter" class="input-form" style="width: 200px;">
            <option value="">All Statuses</option>
            <option value="pending_sr" selected>Pending SR</option>
            <option value="pending_manager">Pending Manager</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Return ID</th>
                    <th>Branch</th>
                    <th>SR Name</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="desktopTable">
                @include('pages.admin.return.table')
            </tbody>
        </table>
    </div>

    <div class="manage-mobile-cards" id="mobileTable">
        @include('pages.admin.return.mtable')
    </div>

</div>

<div class="mt-3">
    {{ $returns->links() }}
</div>

@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');

    function fetchData() {
        let query = searchInput.value;
        let status = statusFilter.value;

        fetch(`{{ route('admin.return.index') }}?search=${query}&status=${status}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('desktopTable').innerHTML = data.table;
            if (data.mobile) {
                document.getElementById('mobileTable').innerHTML = data.mobile;
            }
        });
    }

    searchInput.addEventListener('keyup', fetchData);
    statusFilter.addEventListener('change', fetchData);
</script>
@endpush
