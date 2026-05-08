@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>My Returns</h2>
        <p>Manage return requests</p>
        @include('components.alert')
    </div>
    
    <div style="margin: 15px 0; display: flex; gap: 10px;">
        <input type="text" id="search" class="input-form" placeholder="Search by BRET ID or Customer name..." style="flex: 1;">
        <a href="{{ route('sr.return.create') }}" class="btn-smart btn-blue">
            <i class="fas fa-plus me-1"></i> New Return
        </a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Return ID</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="desktopTable">
                @include('pages.sr.return.table')
            </tbody>
        </table>
    </div>
    
    <div class="manage-mobile-cards" id="mobileTable">
        @include('pages.sr.return.mtable')
    </div>

</div>

<div class="mt-3">
    {{ $returns->links() }}
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('search').addEventListener('keyup', function () {
        let query = this.value;

        fetch(`{{ route('sr.return.index') }}?search=${query}`, {
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
