@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0">Branch Employees</h2>
            <p class="text-muted mb-0">Manage registered Branch Employees</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
                <i class="fas fa-id-badge me-1"></i> Total Employees: <span id="totalEmployeeCount">0</span>
            </div>
            <a href="{{ route('manager.employees.create') }}" class="btn-smart btn-blue">
                <i class="fas fa-plus me-1"></i> Add New Employee
            </a>
        </div>
    </div>

    @include('components.alert')

    {{-- Smart Filter Bar --}}
    <div class="smart-filter-wrapper">
        <div class="smart-filter-grid-3">

            {{-- Search --}}
            <div>
                <label>Search</label>
                <div style="position: relative;">
                    <input type="text" id="searchInput" class="input-form" placeholder="Search by Name or Employee ID (e.g. BRE1001)..." value="{{ request('search') }}" style="padding-left: 32px;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                </div>
            </div>

            {{-- Rank Filter --}}
            <div>
                <label>Rank Filter</label>
                <select id="rankFilter" class="input-form">
                    <option value="">-- All Ranks --</option>
                    @foreach($ranks as $r)
                    <option value="{{ $r }}" {{ request('rank') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Button --}}
            <div>
                <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters & Show All" style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
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
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view employees.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="manage-mobile-cards" id="employeeMobile">
        <p class="text-center text-muted py-5">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view employees.
        </p>
    </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.getElementById('searchInput');
    const rankFilter        = document.getElementById('rankFilter');
    const resetBtn          = document.getElementById('resetBtn');

    const employeeTable     = document.getElementById('employeeTable');
    const employeeMobile    = document.getElementById('employeeMobile');
    const totalCountEl      = document.getElementById('totalEmployeeCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (employeeTable) {
            employeeTable.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading employees...
                    </td>
                </tr>`;
        }
        if (employeeMobile) {
            employeeMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading employees...
                </p>`;
        }
    }

    function showErrorState() {
        if (employeeTable) {
            employeeTable.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load employee data. Please try again.
                    </td>
                </tr>`;
        }
        if (employeeMobile) {
            employeeMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load employee data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput) searchInput.value = '';
        if (rankFilter)  rankFilter.value  = '';
    }

    function fetchFilteredEmployees(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const rank   = encodeURIComponent(rankFilter ? rankFilter.value : '');
            url = `{{ route('manager.employees.index.data') }}?search=${search}&rank=${rank}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (employeeTable)  employeeTable.innerHTML  = data.table;
            if (employeeMobile) employeeMobile.innerHTML = data.mobile;
            if (totalCountEl && data.total !== undefined) {
                totalCountEl.innerText = data.total;
            }
            if (paginationWrapper && data.pagination !== undefined) {
                paginationWrapper.innerHTML = data.pagination;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showErrorState();
        });
    }

    // Initial Load: Only fetch if search/rank/page parameters exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredEmployees();
    }

    // Debounce search input
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredEmployees(), 350);
        });
    }

    // Rank filter change listener
    if (rankFilter) {
        rankFilter.addEventListener('change', () => fetchFilteredEmployees());
    }

    // Reset button handler
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredEmployees();
        });
    }

    // AJAX pagination handling
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredEmployees(link.href);
            }
        });
    }
});
</script>
@endpush