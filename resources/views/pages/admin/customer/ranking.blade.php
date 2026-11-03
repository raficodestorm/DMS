@extends(getLayout())

@section('content')
<div class="manage-card">

    {{-- Top Header Section --}}
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <div class="ranking-header-icon">
                    <i class="fas fa-trophy text-warning"></i>
                </div>
                <div>
                    <h2 class="mb-0 fw-bold">Wholesale Customer Ranking</h2>
                    <p class="text-muted mb-0 small">
                        Ranked by total order amount in the last 30 days ({{ now()->subDays(30)->format('d M, Y') }} - {{ now()->format('d M, Y') }})
                    </p>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(auth()->user()->role === 'admin')
                <div class="total-badge-card">
                    <i class="fas fa-crown me-1 text-warning"></i> Total Wholesale: <span id="totalCustomerCount">0</span>
                </div>
            @endif
            
        </div>
    </div>

    @include('components.alert')

    {{-- Quick KPI Stats Grid --}}
    <div class="ranking-stats-grid mb-4">
        <div class="ranking-stat-card">
            <div class="stat-icon-wrapper bg-gold-soft">
                <i class="fas fa-crown text-warning"></i>
            </div>
            <div class="stat-content">
                <small class="text-muted">#1 Top Customer (30 Days)</small>
                <h5 class="mb-0 fw-bold text-truncate" id="statTopCustomer" style="max-width: 240px;">Loading...</h5>
                <span class="text-success fw-bold small">৳ <span id="statTopAmount">0.00</span></span>
            </div>
        </div>

        <div class="ranking-stat-card">
            <div class="stat-icon-wrapper bg-purple-soft">
                <i class="fas fa-calendar-alt text-purple"></i>
            </div>
            <div class="stat-content">
                <small class="text-muted">Ranking Window</small>
                <h6 class="mb-0 fw-bold">Last 30 Days</h6>
                <span class="text-muted small">{{ now()->subDays(30)->format('d M') }} - {{ now()->format('d M, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Smart Filter Bar --}}
    <div class="smart-filter-wrapper">
        <div class="smart-filter-grid-4">

            {{-- Show (Limit) Filter --}}
            <div>
                <label><i class="fas fa-list-ol me-1"></i> Show Option</label>
                <select id="limitFilter" class="input-form fw-semibold" style="cursor: pointer;">
                    <option value="10" selected>Top 10 Customers</option>
                    <option value="20">Top 20 Customers</option>
                    <option value="30">Top 30 Customers</option>
                    <option value="40">Top 40 Customers</option>
                    <option value="50">Top 50 Customers</option>
                    <option value="all">All Wholesale Customers</option>
                </select>
            </div>

            {{-- Search Filter --}}
            <div>
                <label><i class="fas fa-search me-1"></i> Search</label>
                <div style="position: relative;">
                    <input type="text" id="search" class="input-form" placeholder="Search by Shop Name or BRC200 ID..." value="" style="padding-left: 32px;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                </div>
            </div>

            {{-- Branch Filter --}}
            <div>
                <label><i class="fas fa-code-branch me-1"></i> Branch</label>
                <select id="branchFilter" class="input-form">
                    <option value="">-- All Branches --</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Button --}}
            <div>
                <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters & Show Top 10" style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                    <i class="fas fa-undo"></i>
                </button>
            </div>

        </div>
    </div>

    {{-- Table Wrapper --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 80px;">Rank</th>
                    <th>Customer ID</th>
                    <th>Shop Name</th>
                    <th>Area Branch</th>
                    @if(auth()->user()->role === 'admin')
                        <th>Referred SR</th>
                    @endif
                    <th class="text-center">30-Day Orders</th>
                    <th class="text-end">30-Day Order Amount</th>
                    @if(auth()->user()->role === 'admin')
                        <th class="text-end">Current Due</th>
                    @endif
                    <th class="text-center" style="width: 80px;">Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="rankingTable">
                <tr>
                    <td colspan="{{ auth()->user()->role === 'admin' ? 9 : 7 }}" class="text-center py-5 text-muted">
                        <i class="fas fa-spinner fa-spin me-2 text-primary"></i> Loading customer ranking...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Mobile View --}}
    <div class="manage-mobile-cards" id="rankingMobile">
        <p class="text-center text-muted py-5">
            <i class="fas fa-spinner fa-spin me-2 text-primary"></i> Loading customer ranking...
        </p>
    </div>

</div>

{{-- Customer Ranking Position Details Modal --}}
<div id="rankingPositionModal" class="custom-modal-overlay" onclick="handleRankingModalBackdrop(event)">
    <div class="custom-modal-box">
        <div class="custom-modal-header">
            <div class="d-flex align-items-center gap-2">
                <div class="modal-icon-badge" id="modalHeaderBadge">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" id="modalHeaderTitle">Ranking Position Details</h5>
                    <small class="text-muted" id="modalHeaderSubtitle">Wholesale Customer Performance Analysis</small>
                </div>
            </div>
            <button type="button" class="custom-modal-close-btn" onclick="closeRankingPositionModal()">&times;</button>
        </div>
        <div class="custom-modal-body p-3 p-md-4" id="rankingPositionModalContent">
            {{-- Content loaded dynamically via AJAX --}}
        </div>
    </div>
</div>

{{-- Pagination Links --}}
<div class="mt-3" id="paginationWrapper"></div>

<style>
    /* Ranking Page Specific Styles */
    .ranking-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(245, 158, 11, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .total-badge-card {
        background: rgba(49, 49, 255, 0.08);
        color: var(--primary);
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
        border: 1px solid rgba(49, 49, 255, 0.2);
    }
    .ranking-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    @media (max-width: 576px) {
        .ranking-stats-grid {
            grid-template-columns: 1fr;
        }
    }
    .ranking-stat-card {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .ranking-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.06);
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .bg-gold-soft {
        background: rgba(245, 158, 11, 0.14);
    }
    .bg-purple-soft {
        background: rgba(139, 92, 246, 0.14);
    }
    .text-purple {
        color: #8b5cf6;
    }
    .customer-avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    /* Rank Badges */
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.88rem;
        padding: 4px 10px;
        border-radius: 8px;
        min-width: 45px;
    }
    .rank-1 {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        border: 1px solid #f59e0b;
        box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
    }
    .rank-2 {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #334155;
        border: 1px solid #94a3b8;
    }
    .rank-3 {
        background: linear-gradient(135deg, #ffedd5, #fed7aa);
        color: #9a3412;
        border: 1px solid #ea580c;
    }
    .rank-normal {
        background: var(--background);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }
    .orders-count-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(59, 130, 246, 0.08);
        color: var(--primary);
        font-weight: 600;
        font-size: 0.82rem;
    }
    .sales-amount-highlight {
        font-weight: 700;
        font-size: 0.9rem;
        color: #10b981;
    }

    /* Modal Styles */
    .custom-modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.65);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center; justify-content: center;
        padding: 16px;
        animation: fadeIn 0.2s ease;
    }
    .custom-modal-overlay.show {
        display: flex;
    }
    .custom-modal-box {
        background: var(--section-bg, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 16px;
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 15px 45px rgba(0,0,0,0.25);
        animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .custom-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--section-bg, #ffffff);
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .modal-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .custom-modal-close-btn {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 8px;
        transition: 0.2s ease;
    }
    .custom-modal-close-btn:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    .customer-avatar-lg {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(59, 130, 246, 0.12);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const limitFilter       = document.getElementById('limitFilter');
    const searchInput       = document.getElementById('search');
    const branchFilter      = document.getElementById('branchFilter');
    const resetBtn          = document.getElementById('resetBtn');

    const rankingTable      = document.getElementById('rankingTable');
    const rankingMobile     = document.getElementById('rankingMobile');
    const totalCountEl      = document.getElementById('totalCustomerCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    const statTopCustomer   = document.getElementById('statTopCustomer');
    const statTopAmount     = document.getElementById('statTopAmount');

    function showLoadingState() {
        if (rankingTable) {
            rankingTable.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="fas fa-spinner fa-spin me-2 text-primary"></i> Loading customer ranking...
                    </td>
                </tr>`;
        }
        if (rankingMobile) {
            rankingMobile.innerHTML = `
                <p class="text-center text-muted py-5">
                    <i class="fas fa-spinner fa-spin me-2 text-primary"></i> Loading customer ranking...
                </p>`;
        }
    }

    function showErrorState() {
        if (rankingTable) {
            rankingTable.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load customer ranking. Please try again.
                    </td>
                </tr>`;
        }
        if (rankingMobile) {
            rankingMobile.innerHTML = `
                <p class="text-center text-danger py-5">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load customer ranking.
                </p>`;
        }
    }

    function clearAllFilters() {
        if (limitFilter)  limitFilter.value  = '10';
        if (searchInput)  searchInput.value  = '';
        if (branchFilter) branchFilter.value = '';
    }

    function fetchRanking(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const limit  = encodeURIComponent(limitFilter ? limitFilter.value : '10');
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const branch = encodeURIComponent(branchFilter ? branchFilter.value : '');

            const rankingDataUrl = '{{ match(auth()->user()->role) { "admin" => route("admin.customers.ranking.data"), "sr" => route("sr.customers.ranking.data"), default => route("manager.customers.ranking.data") } }}';
            url = `${rankingDataUrl}?limit=${limit}&search=${search}&branch_id=${branch}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (rankingTable)  rankingTable.innerHTML  = data.table;
            if (rankingMobile) rankingMobile.innerHTML = data.mobile;
            if (totalCountEl && data.total !== undefined) {
                totalCountEl.innerText = data.total;
            }
            if (paginationWrapper && data.pagination !== undefined) {
                // If limit is 'all', pagination is not necessary
                paginationWrapper.innerHTML = (limitFilter && limitFilter.value === 'all') ? '' : data.pagination;
            }

            // Update Statistics Cards
            if (statTopCustomer && data.top_customer) {
                statTopCustomer.innerText = data.top_customer;
            }
            if (statTopAmount && data.top_amount) {
                statTopAmount.innerText = data.top_amount;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showErrorState();
        });
    }

    // Initial Load: Fetch top 10 customers on page ready
    fetchRanking();

    // Event Listeners
    if (limitFilter) {
        limitFilter.addEventListener('change', () => fetchRanking());
    }

    if (branchFilter) {
        branchFilter.addEventListener('change', () => fetchRanking());
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchRanking(), 350);
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilters();
            fetchRanking();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchRanking(link.href);
            }
        });
    }

    // ==================== Ranking Position Details Modal ====================
    const modalEl = document.getElementById('rankingPositionModal');
    const modalContent = document.getElementById('rankingPositionModalContent');

    window.openRankingPositionModal = function(customerId, rank) {
        if (!modalEl || !modalContent) return;

        // Show modal with loading state
        modalContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-3 text-muted fw-semibold">Loading position details...</div>
            </div>`;
        modalEl.classList.add('show');
        document.body.style.overflow = 'hidden';

        const rankingPosBaseUrl = '{{ match(auth()->user()->role) { "admin" => url("/admin/customers"), "sr" => url("/sr/customers"), default => url("/manager/customers") } }}';
        const url = `${rankingPosBaseUrl}/${customerId}/ranking-position?rank=${encodeURIComponent(rank || '')}`;
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to load ranking details');
            return res.json();
        })
        .then(data => {
            if (data.success && data.html) {
                modalContent.innerHTML = data.html;
            } else {
                throw new Error('Invalid response structure');
            }
        })
        .catch(err => {
            console.error('Modal fetch error:', err);
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-danger fs-1 mb-2"></i>
                    <h5 class="fw-bold text-danger">Failed to Load Details</h5>
                    <p class="text-muted small">Could not retrieve position details for this customer. Please try again.</p>
                    <button type="button" class="btn-smart btn-blue mt-2" onclick="openRankingPositionModal('${customerId}', '${rank}')">
                        <i class="fas fa-redo me-1"></i> Retry
                    </button>
                </div>`;
        });
    };

    window.closeRankingPositionModal = function() {
        if (modalEl) {
            modalEl.classList.remove('show');
            document.body.style.overflow = '';
        }
    };

    window.handleRankingModalBackdrop = function(event) {
        if (event.target === modalEl) {
            closeRankingPositionModal();
        }
    };

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalEl && modalEl.classList.contains('show')) {
            closeRankingPositionModal();
        }
    });

    // Delegate click event for .btn-ranking-details (works for AJAX reloaded table and cards)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-ranking-details');
        if (btn) {
            e.preventDefault();
            const customerId = btn.getAttribute('data-id');
            const rank = btn.getAttribute('data-rank');
            if (customerId) {
                openRankingPositionModal(customerId, rank);
            }
        }
    });
});
</script>
@endpush
