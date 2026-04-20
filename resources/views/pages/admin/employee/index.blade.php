@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>All Employees</h2>
        <p>Manage all registered Employees</p>
    </div>
    <div style="margin: 15px 0;">
        <input type="text" id="search" class="input-form" placeholder="Search by Name or ID...">
    </div>
    @include('components.alert')
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Rank</th>
                    <th>Phone</th>
                    <th>Branch</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="employeeTable">
                @include('pages.admin.employee.table')
            </tbody>
        </table>
    </div>
    <div class="manage-mobile-cards" id="employeeMobile">
        @include('pages.admin.employee.mtable')
    </div>


</div>

@endsection

@push('scripts')
<script>
    document.getElementById('search').addEventListener('keyup', function () {
    let query = this.value;

    fetch(`{{ route('admin.employees.index') }}?search=${query}`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
.then(res => res.json())
.then(data => {
    document.getElementById('employeeTable').innerHTML = data.table;
    document.getElementById('employeeMobile').innerHTML = data.mobile;
});
});
</script>
@endpush