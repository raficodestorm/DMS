@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0">All Employees</h2>
            <p class="text-muted mb-0">Manage all registered Employees</p>
        </div>
        <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
            <i class="fas fa-id-badge me-1"></i> Total Employees: <span id="totalEmployeeCount">{{ $employees->count() }}</span>
        </div>
    </div>

    @include('components.alert')

    {{-- Smart Filter Bar --}}
    <div style="margin: 15px 0; background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
        <div class="row g-2 align-items-end">
            {{-- Search --}}
            <div class="col-12 col-md-5 col-lg-5">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
                <div style="position: relative;">
                    <input type="text" id="search" class="input-form" placeholder="Search by Name or BRE100 ID..." style="margin-bottom: 0; padding-left: 35px; height: 42px;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            {{-- Rank Filter --}}
            <div class="col-6 col-md-3 col-lg-3">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Rank</label>
                <select id="rankFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
                    <option value="">-- All Ranks --</option>
                    <option value="SR">SR</option>
                    <option value="TSM">TSM</option>
                    <option value="Manager">Manager</option>
                    <option value="DSO">DSO</option>
                    <option value="Cooperator">Cooperator</option>
                </select>
            </div>

            {{-- Branch Filter --}}
            <div class="col-6 col-md-3 col-lg-3">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Branch</label>
                <select id="branchFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
                    <option value="">-- All Branches --</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset --}}
            <div class="col-12 col-md-1 col-lg-1">
                <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100" title="Reset Filters" style="height: 42px; display: inline-flex; align-items: center; justify-content: center;">
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
document.addEventListener('DOMContentLoaded', function () {
    const searchInput  = document.getElementById('search');
    const rankFilter   = document.getElementById('rankFilter');
    const branchFilter = document.getElementById('branchFilter');
    const resetBtn     = document.getElementById('resetBtn');

    function fetchFilteredEmployees() {
        const query  = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
        const rank   = encodeURIComponent(rankFilter ? rankFilter.value : '');
        const branch = encodeURIComponent(branchFilter ? branchFilter.value : '');

        const url = `{{ route('admin.employees.index') }}?search=${query}&rank=${rank}&branch_id=${branch}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('employeeTable').innerHTML = data.table;
            document.getElementById('employeeMobile').innerHTML = data.mobile;
            if (document.getElementById('totalEmployeeCount') && data.total !== undefined) {
                document.getElementById('totalEmployeeCount').innerText = data.total;
            }
        })
        .catch(err => console.error('Filter fetch error:', err));
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchFilteredEmployees, 350);
        });
    }

    if (rankFilter) rankFilter.addEventListener('change', fetchFilteredEmployees);
    if (branchFilter) branchFilter.addEventListener('change', fetchFilteredEmployees);

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (rankFilter) rankFilter.value = '';
            if (branchFilter) branchFilter.value = '';
            fetchFilteredEmployees();
        });
    }
});
</script>
@endpush