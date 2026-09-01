@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>Return Management</h2>
        <p>Review and process return requests from SRs</p>
        @include('components.alert')
    </div>
    
    <div style="margin: 15px 0;">
        <input type="text" id="search" class="input-form" placeholder="Search by BRET ID, SR, or Customer name...">
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Return ID</th>
                    <th>SR Name</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="desktopTable">
                @include('pages.manager.return.table')
            </tbody>
        </table>
    </div>

    <div class="manage-mobile-cards" id="mobileTable">
        @include('pages.manager.return.mtable')
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

        fetch(`{{ route('manager.return.index') }}?search=${query}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('desktopTable').innerHTML = data.table;
            if (data.mobile) {
                document.getElementById('mobileTable').innerHTML = data.mobile;
            }
        });
    });
</script>
@endpush
