@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>Branch Employees</h2>
        <p>Manage own registered Employees</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    {{-- Smart Filter Bar --}}
    <div style="margin: 15px 0; background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
        <div class="row g-2 align-items-end">
            {{-- Search Bar --}}
            <div class="col-12 col-md-7 col-lg-7">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
                <div style="position: relative;">
                    <input type="text" id="search" class="input-form" placeholder="Search by Name or Employee ID (e.g. BRE1001)..." value="{{ request('search') }}" style="margin-bottom: 0; padding-left: 35px; height: 42px;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                </div>
            </div>

            {{-- Rank Filter --}}
            <div class="col-8 col-md-4 col-lg-4">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Rank Filter</label>
                <select id="rankFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
                    <option value="">-- All Ranks --</option>
                    @foreach($ranks as $r)
                    <option value="{{ $r }}" {{ request('rank') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Button --}}
            <div class="col-4 col-md-1 col-lg-1">
                <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100" title="Reset Filters" style="margin-bottom: 0; height: 42px; display: inline-flex; align-items: center; justify-content: center;">
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
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="employeeTable">
                @include('pages.manager.employee.table')
            </tbody>
        </table>
    </div>
    <div class="manage-mobile-cards" id="employeeMobile">
        @include('pages.manager.employee.mtable')
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const rankFilter  = document.getElementById('rankFilter');
    const resetBtn    = document.getElementById('resetBtn');

    function fetchFilteredEmployees() {
        const query = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
        const rank  = encodeURIComponent(rankFilter ? rankFilter.value : '');

        const url = `{{ route('manager.employees.index') }}?search=${query}&rank=${rank}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('employeeTable').innerHTML = data.table;
            document.getElementById('employeeMobile').innerHTML = data.mobile;
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

    if (rankFilter) {
        rankFilter.addEventListener('change', fetchFilteredEmployees);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (rankFilter) rankFilter.value = '';
            fetchFilteredEmployees();
        });
    }
});
</script>
@endpush