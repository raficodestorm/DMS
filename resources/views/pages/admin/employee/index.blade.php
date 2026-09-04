@extends(getLayout())

@section('content')
<div class="manage-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0">All Employees</h2>
            <p class="text-muted mb-0">Manage all registered Employees</p>
        </div>
        <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
            <i class="fas fa-id-badge me-1"></i> Total Employees: <span id="totalEmployeeCount">0</span>
        </div>
    </div>

    @include('components.alert')

    {{-- Smart Filter Bar --}}
    <div class="smart-filter-wrapper">
        <div class="smart-filter-grid-5">

            {{-- Search --}}
            <div>
                <label>Search</label>
                <div style="position: relative;">
                    <input type="text" id="search" class="input-form" placeholder="Search by Name or BRE100 ID..." style="padding-left: 32px;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                </div>
            </div>

            {{-- Rank Filter --}}
            <div>
                <label>Rank</label>
                <select id="rankFilter" class="input-form">
                    <option value="">-- All Ranks --</option>
                    <option value="SR">SR</option>
                    <option value="TSM">TSM</option>
                    <option value="Manager">Manager</option>
                    <option value="DSO">DSO</option>
                    <option value="Cooperator">Cooperator</option>
                </select>
            </div>

            {{-- Branch Filter --}}
            <div>
                <label>Branch</label>
                <select id="branchFilter" class="input-form">
                    <option value="">-- All Branches --</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
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
                    <th>Branch</th>
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
    const searchInput       = document.getElementById('search');
    const rankFilter        = document.getElementById('rankFilter');
    const branchFilter      = document.getElementById('branchFilter');
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
        if (searchInput)  searchInput.value  = '';
        if (rankFilter)   rankFilter.value   = '';
        if (branchFilter) branchFilter.value = '';
    }

    function fetchFilteredEmployees(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const rank   = encodeURIComponent(rankFilter ? rankFilter.value : '');
            const branch = encodeURIComponent(branchFilter ? branchFilter.value : '');

            url = `{{ route('admin.employees.index.data') }}?search=${search}&rank=${rank}&branch_id=${branch}`;
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

    // Initial Load: Only fetch if filters/page params exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredEmployees();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredEmployees(), 350);
        });
    }

    if (rankFilter)   rankFilter.addEventListener('change',   () => fetchFilteredEmployees());
    if (branchFilter) branchFilter.addEventListener('change', () => fetchFilteredEmployees());

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredEmployees();
        });
    }

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