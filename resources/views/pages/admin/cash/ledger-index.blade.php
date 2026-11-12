@extends('layouts.adminlayout')

@section('content')

{{-- KPI Summary Cards Strip --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%); border-left: 4px solid var(--primary, #3131ff) !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Current Admin Cash</span>
                    <h3 class="fw-bold mb-0 text-primary-custom" style="font-size: 1.4rem;">৳ {{ number_format($totalAdminCash, 2) }}</h3>
                </div>
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(49,49,255,0.12); color: var(--primary, #3131ff); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #fff; border-left: 4px solid #16a34a !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Inflow (Cash In + Transfer)</span>
                    <h3 class="fw-bold mb-0 text-success" style="font-size: 1.4rem;">৳ {{ number_format($totalInflow, 2) }}</h3>
                </div>
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(22,163,74,0.12); color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-circle-arrow-down"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #fff; border-left: 4px solid #dc2626 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Outflow (Supplier + Expense)</span>
                    <h3 class="fw-bold mb-0 text-danger" style="font-size: 1.4rem;">৳ {{ number_format($totalOutflow, 2) }}</h3>
                </div>
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(220,38,38,0.12); color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-circle-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #fff; border-left: 4px solid #64748b !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Ledger Records</span>
                    <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.4rem;"><span id="totalLedgerCountBadge">{{ $totalEntries }}</span></h3>
                </div>
                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(100,116,139,0.12); color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="manage-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0"><i class="fa-solid fa-book-bookmark text-primary me-2"></i> Admin Cash Ledger</h2>
            <p class="text-muted mb-0">Complete audit trail of all central treasury cash movements</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
                <i class="fas fa-receipt me-1"></i> Total Entries: <span id="totalLedgerCount">{{ $totalEntries }}</span>
            </div>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#cashInModal" style="border-radius: 10px; font-size: 0.85rem; font-weight: 600; height: 36px; display: inline-flex; align-items: center; gap: 6px; padding: 0 16px; background: #16a34a; border: none; color: #fff;">
                <i class="fa-solid fa-plus-circle"></i> Cash In
            </button>
            <a href="{{ route('admin.cash.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 10px; font-size: 0.85rem; font-weight: 600; height: 36px; display: inline-flex; align-items: center; gap: 6px; padding: 0 16px;">
                <i class="fa-solid fa-vault"></i> Cash Treasury
            </a>
        </div>
    </div>

    @include('components.alert')

    {{-- Smart Filter Bar --}}
    <div class="smart-filter-wrapper">
        <div class="smart-filter-grid-7" style="grid-template-columns: repeat(6, 1fr) 50px;">

            {{-- Search --}}
            <div>
                <label>Search</label>
                <div style="position: relative;">
                    <input type="text" id="searchInput" class="input-form" placeholder="ID, Note, Method, Admin..." value="{{ request('search') }}" style="padding-left: 30px;">
                    <i class="fas fa-search" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem;"></i>
                </div>
            </div>

            {{-- Type Filter --}}
            <div>
                <label>Type</label>
                <select id="typeFilter" class="input-form">
                    <option value="">-- All Types --</option>
                    <option value="cash-in" {{ request('type') == 'cash-in' ? 'selected' : '' }}>Cash In</option>
                    <option value="cash_transfer" {{ request('type') == 'cash_transfer' ? 'selected' : '' }}>Branch Transfer</option>
                    <option value="supplier_payment" {{ request('type') == 'supplier_payment' ? 'selected' : '' }}>Supplier Payment</option>
                    <option value="company_cost" {{ request('type') == 'company_cost' ? 'selected' : '' }}>Company Cost</option>
                </select>
            </div>

            {{-- Payment Method Filter --}}
            <div>
                <label>Payment Method</label>
                <select id="methodFilter" class="input-form">
                    <option value="">-- All Methods --</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="bkash" {{ request('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                </select>
            </div>

            {{-- From Date --}}
            <div>
                <label>From Date</label>
                <input type="date" id="fromDate" class="input-form" value="{{ request('from_date') }}">
            </div>

            {{-- To Date --}}
            <div>
                <label>To Date</label>
                <input type="date" id="toDate" class="input-form" value="{{ request('to_date') }}">
            </div>

            {{-- Quick Date Range Helper --}}
            <div>
                <label>Quick Period</label>
                <select id="quickPeriod" class="input-form">
                    <option value="">All Time</option>
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                </select>
            </div>

            {{-- Reset Button --}}
            <div>
                <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters & Show All" style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; margin-top: 22px;">
                    <i class="fas fa-undo"></i>
                </button>
            </div>

        </div>
    </div>

    {{-- Desktop Table --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Ledger ID</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Cash Before</th>
                    <th>Cash After</th>
                    <th>Method</th>
                    <th>Recorded By / Note</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="desktopTable">
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading cash ledger...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="manage-mobile-cards" id="mobileTable">
        <p class="text-center text-muted py-5">
            <i class="fas fa-spinner fa-spin me-2"></i> Loading cash ledger...
        </p>
    </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

{{-- Cash In Modal --}}
<div class="modal fade" id="cashInModal" tabindex="-1" aria-labelledby="cashInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px; border: 1px solid var(--border-color, #e2e8f0); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-bottom: 1px solid #bbf7d0; padding: 18px 24px;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #16a34a; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.25);">
                        <i class="fa-solid fa-plus-circle"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-success" id="cashInModalLabel">Admin Cash In</h5>
                        <small class="text-muted">Deposit / inject capital into Central Admin Cash</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.cash.cash-in') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Balance Pill --}}
                    <div class="d-flex align-items-center justify-content-between p-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                        <span class="text-muted small fw-semibold">Current Admin Cash:</span>
                        <strong class="text-primary-custom" style="font-size: 1.1rem;">৳ {{ number_format($totalAdminCash, 2) }}</strong>
                    </div>

                    {{-- Amount Input --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Cash In Amount (৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="input-form" placeholder="0.00" required autofocus style="font-size: 1.1rem; font-weight: 700;">
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Payment / Deposit Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="input-form" required>
                            <option value="cash" selected>Cash</option>
                            <option value="bank">Bank Transfer / Deposit</option>
                            <option value="cheque">Cheque</option>
                            <option value="bkash">bKash / Mobile Banking</option>
                        </select>
                    </div>

                    {{-- Note / Source --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Note / Source / Reference <small class="text-muted fw-normal">(Optional)</small></label>
                        <input type="text" name="note" class="input-form" placeholder="e.g. Owner capital, Bank cash withdrawal, etc.">
                    </div>

                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px;">
                        <small class="text-success d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>This amount will be added to Central Admin Cash and logged in the <strong>Admin Cash Ledger</strong> as <code>cash-in</code>.</span>
                        </small>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-success" style="border-radius: 8px; font-weight: 700; padding: 8px 20px; background: #16a34a; border: none;">
                        <i class="fa-solid fa-arrow-down-to-bracket me-1"></i> Confirm Cash In
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('searchInput');
    const typeFilter    = document.getElementById('typeFilter');
    const methodFilter  = document.getElementById('methodFilter');
    const fromDate      = document.getElementById('fromDate');
    const toDate        = document.getElementById('toDate');
    const quickPeriod   = document.getElementById('quickPeriod');
    const resetBtn      = document.getElementById('resetBtn');

    const desktopTable        = document.getElementById('desktopTable');
    const mobileTable         = document.getElementById('mobileTable');
    const totalCountEl        = document.getElementById('totalLedgerCount');
    const totalCountBadgeEl   = document.getElementById('totalLedgerCountBadge');
    const paginationWrapper   = document.getElementById('paginationWrapper');

    let debounceTimer;

    function showLoadingState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading cash ledger...
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading cash ledger...
                </p>`;
        }
    }

    function showErrorState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load ledger data. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load ledger data.
                </p>`;
        }
    }

    function fetchLedgers(url) {
        showLoadingState();

        const params = new URLSearchParams({
            search:         searchInput ? searchInput.value.trim() : '',
            type:           typeFilter ? typeFilter.value : '',
            payment_method: methodFilter ? methodFilter.value : '',
            from_date:      fromDate ? fromDate.value : '',
            to_date:        toDate ? toDate.value : '',
        });

        let targetUrl = url || "{{ route('admin.cash.ledger.data') }}";
        const separator = targetUrl.includes('?') ? '&' : '?';
        targetUrl = `${targetUrl}${separator}${params.toString()}`;

        fetch(targetUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response error');
            return response.json();
        })
        .then(data => {
            if (desktopTable)      desktopTable.innerHTML      = data.table;
            if (mobileTable)       mobileTable.innerHTML       = data.mobile;
            if (paginationWrapper) paginationWrapper.innerHTML = data.pagination || '';
            if (totalCountEl)      totalCountEl.textContent    = data.total || 0;
            if (totalCountBadgeEl) totalCountBadgeEl.textContent = data.total || 0;
            bindPaginationLinks();
        })
        .catch(err => {
            console.error(err);
            showErrorState();
        });
    }

    function bindPaginationLinks() {
        if (!paginationWrapper) return;
        const links = paginationWrapper.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url) fetchLedgers(url);
            });
        });
    }

    // Input listeners with debounce
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchLedgers(), 350);
        });
    }

    [typeFilter, methodFilter, fromDate, toDate].forEach(element => {
        if (element) {
            element.addEventListener('change', () => fetchLedgers());
        }
    });

    // Quick period selector
    if (quickPeriod) {
        quickPeriod.addEventListener('change', function () {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;

            if (this.value === 'today') {
                fromDate.value = todayStr;
                toDate.value   = todayStr;
            } else if (this.value === 'this_week') {
                const day = today.getDay();
                const diff = today.getDate() - day + (day === 0 ? -6 : 1);
                const monday = new Date(today.setDate(diff));
                const monStr = `${monday.getFullYear()}-${String(monday.getMonth()+1).padStart(2,'0')}-${String(monday.getDate()).padStart(2,'0')}`;
                fromDate.value = monStr;
                toDate.value   = todayStr;
            } else if (this.value === 'this_month') {
                fromDate.value = `${yyyy}-${mm}-01`;
                toDate.value   = todayStr;
            } else {
                fromDate.value = '';
                toDate.value   = '';
            }
            fetchLedgers();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (searchInput)  searchInput.value  = '';
            if (typeFilter)   typeFilter.value   = '';
            if (methodFilter) methodFilter.value = '';
            if (fromDate)     fromDate.value     = '';
            if (toDate)       toDate.value       = '';
            if (quickPeriod)  quickPeriod.value  = '';
            fetchLedgers();
        });
    }

    // Initial load
    fetchLedgers();
});
</script>
@endpush

