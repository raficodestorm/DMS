@extends('layouts.adminlayout')

@section('content')
<div class="cash-dashboard-container">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-vault-icon">
                    <i class="fa-solid fa-vault"></i>
                </span>
                <h2 class="mb-0 fw-bold title-heading">Company Cash Treasury</h2>
            </div>
            <p class="text-muted mb-0 small">
                Real-time central overview of company reserves and branch cash balances.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="live-sync-badge">
                <span class="pulse-dot"></span> Live Balance
            </span>
            <button onclick="window.location.reload();" class="btn-refresh" title="Refresh Cash">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </div>

    {{-- 1. BIG HERO CARD: Grand Company Current Cash --}}
    <div class="hero-cash-card mb-4 animate__animated animate__fadeIn">
        <div class="hero-bg-overlay"></div>
        <div class="hero-content">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="hero-badge">
                            <i class="fa-solid fa-shield-halved me-1"></i> Total Liquidity
                        </span>
                        <span class="text-white-50 small">Central Admin + All Active Branches</span>
                    </div>
                    <span class="hero-label">COMPANY CURRENT CASH</span>
                    <h1 class="hero-amount">
                        <span class="currency-symbol">৳</span> {{ number_format($totalCompanyCash, 2) }}
                    </h1>
                </div>

                {{-- Visual Ratio Breakdown --}}
                <div class="hero-distribution-box">
                    <div class="d-flex justify-content-between align-items-center text-white-50 small mb-2">
                        <span><i class="fa-solid fa-user-shield me-1"></i> Admin: {{ $adminSharePercent }}%</span>
                        <span><i class="fa-solid fa-code-branch me-1"></i> Branches: {{ $branchSharePercent }}%</span>
                    </div>
                    <div class="ratio-progress-bar">
                        <div class="progress-segment admin-bar" style="width: {{ $adminSharePercent }}%;" title="Admin Share: {{ $adminSharePercent }}%"></div>
                        <div class="progress-segment branch-bar" style="width: {{ $branchSharePercent }}%;" title="Branches Share: {{ $branchSharePercent }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 pt-1 border-top border-white-10 text-white-50 x-small">
                        <span>৳ {{ number_format($totalAdminCash, 2) }}</span>
                        <span>৳ {{ number_format($totalBranchCash, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SECONDARY HIGHLIGHT CARDS (Admin Cash & Summary Stats) --}}
    <div class="row g-3 g-md-4 mb-4">

        {{-- Admin Cash Card (Medium Highlight) --}}
        <div class="col-12 col-lg-6">
            <div class="secondary-cash-card admin-vault-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble admin-icon-bubble">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div>
                            <span class="card-subtitle-tag">Central Vault</span>
                            <h4 class="card-main-title mb-0">Admin Cash Balance</h4>
                        </div>
                    </div>
                    <span class="share-pill bg-admin-soft">
                        {{ $adminSharePercent }}% of total
                    </span>
                </div>

                <div class="my-3">
                    <h2 class="sub-amount text-primary-custom mb-0">
                        <span class="currency-symbol">৳</span> {{ number_format($totalAdminCash, 2) }}
                    </h2>
                </div>

                @if($adminCashRecords->isNotEmpty())
                    <div class="admin-accounts-list pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center text-muted x-small mb-2">
                            <span>Admin Account Breakdown</span>
                            <span>{{ $adminCashRecords->count() }} Account(s)</span>
                        </div>
                        @foreach($adminCashRecords as $record)
                            <div class="admin-account-item d-flex justify-content-between align-items-center py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-user-circle text-primary-custom opacity-75"></i>
                                    <span class="small fw-semibold text-main-theme">{{ $record->admin->name ?? 'Primary Admin' }}</span>
                                    @if(!empty($record->admin->email))
                                        <span class="text-muted x-small d-none d-sm-inline">({{ $record->admin->email }})</span>
                                    @endif
                                </div>
                                <span class="fw-bold text-main-theme small">৳ {{ number_format($record->cash, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="pt-3 border-top text-muted small">
                        <i class="fa-solid fa-info-circle me-1"></i> Central admin treasury balance.
                    </div>
                @endif
            </div>
        </div>

        {{-- Total Branches Cumulative Cash Card --}}
        <div class="col-12 col-lg-6">
            <div class="secondary-cash-card branch-summary-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble branch-icon-bubble">
                            <i class="fa-solid fa-shop"></i>
                        </div>
                        <div>
                            <span class="card-subtitle-tag">Branches Cumulative</span>
                            <h4 class="card-main-title mb-0">All Branches Cash</h4>
                        </div>
                    </div>
                    <span class="share-pill bg-branch-soft">
                        {{ $branchSharePercent }}% of total
                    </span>
                </div>

                <div class="my-3">
                    <h2 class="sub-amount text-success-custom mb-0">
                        <span class="currency-symbol">৳</span> {{ number_format($totalBranchCash, 2) }}
                    </h2>
                </div>

                <div class="pt-3 border-top row g-2">
                    <div class="col-6">
                        <div class="mini-stat-tile">
                            <span class="text-muted x-small d-block">Active Branches</span>
                            <span class="fw-bold text-main-theme">{{ $branchCount }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mini-stat-tile">
                            <span class="text-muted x-small d-block">Average per Branch</span>
                            <span class="fw-bold text-main-theme">৳ {{ number_format($averageBranchCash, 2) }}</span>
                        </div>
                    </div>
                    @if($topBranch)
                        <div class="col-12 mt-2">
                            <div class="top-holding-tile d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-1 x-small text-muted">
                                    <i class="fa-solid fa-crown text-warning me-1"></i> Highest Cash:
                                    <strong class="text-main-theme ms-1">{{ $topBranch->name }}</strong>
                                </div>
                                <span class="badge bg-success-subtle text-success-custom x-small fw-bold">
                                    ৳ {{ number_format($topBranch->cash, 2) }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- 3. BRANCH-WISE CASH SECTION (Small Cards Grid) --}}
    <div class="branch-section-wrapper mb-4">
        <div class="section-header-bar d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-3">
            <div>
                <h4 class="mb-0 fw-bold section-title">
                    <i class="fa-solid fa-sitemap text-primary-custom me-2"></i>Branch-Wise Cash Breakdown
                </h4>
                <p class="text-muted mb-0 small">Individual cash held across all company branches</p>
            </div>

            {{-- Live Search Filter --}}
            <div class="branch-search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="branchSearchInput" class="branch-search-input" placeholder="Search branch or manager...">
            </div>
        </div>

        @if($branches->isNotEmpty())
            <div class="row g-3 g-md-4" id="branchCardsGrid">
                @foreach($branches as $branch)
                    @php
                        $branchRatio = $totalBranchCash > 0 ? round(($branch->cash / $totalBranchCash) * 100, 1) : 0;
                    @endphp
                    <div class="col-12 col-sm-6 col-lg-4 branch-item-col" 
                         data-name="{{ strtolower($branch->name) }}" 
                         data-manager="{{ strtolower($branch->manager ?? '') }}"
                         data-address="{{ strtolower($branch->address ?? '') }}">
                        <div class="branch-cash-card">
                            
                            {{-- Card Header --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="branch-avatar">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                    <div>
                                        <h5 class="branch-name mb-0 text-truncate" title="{{ $branch->name }}">
                                            {{ $branch->name }}
                                        </h5>
                                        <span class="branch-manager text-muted x-small">
                                            <i class="fa-solid fa-user-tie me-1"></i>{{ $branch->manager ?? 'No Manager' }}
                                        </span>
                                    </div>
                                </div>

                                <span class="badge-ratio" title="{{ $branchRatio }}% of total branch cash">
                                    {{ $branchRatio }}%
                                </span>
                            </div>

                            {{-- Cash Display --}}
                            <div class="branch-cash-amount-box my-3">
                                <span class="cash-caption">Available Cash</span>
                                <h3 class="branch-cash-figure mb-0">
                                    <span class="curr-sign">৳</span> {{ number_format($branch->cash, 2) }}
                                </h3>
                            </div>

                            {{-- Mini Progress Bar --}}
                            <div class="branch-progress-track mb-3">
                                <div class="branch-progress-fill" style="width: {{ min(100, $branchRatio) }}%;"></div>
                            </div>

                            {{-- Card Footer Details --}}
                            <div class="branch-card-footer d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="branch-address text-truncate me-2" title="{{ $branch->address }}">
                                    <i class="fa-solid fa-location-dot text-muted me-1"></i>
                                    <span class="x-small text-muted">{{ $branch->address ? Str::limit($branch->address, 26) : 'N/A' }}</span>
                                </div>
                                <a href="{{ route('admin.branches.edit', $branch->id) }}" class="branch-action-link" title="Manage Branch">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- No Results Message for Search --}}
            <div id="noBranchFound" class="text-center py-5 d-none">
                <div class="empty-icon-wrap mb-2">
                    <i class="fa-solid fa-magnifying-glass fs-1 text-muted opacity-50"></i>
                </div>
                <h5 class="text-muted fw-semibold">No matching branch found</h5>
                <p class="text-muted x-small">Try searching with a different branch name or manager.</p>
            </div>
        @else
            <div class="empty-branch-state text-center py-5 border rounded bg-card">
                <i class="fa-solid fa-store-slash fs-1 text-muted mb-3 opacity-50"></i>
                <h5 class="fw-bold text-main-theme">No Branches Registered</h5>
                <p class="text-muted small mb-3">Create your first branch to start tracking cash flow.</p>
                <a href="{{ route('admin.branches.create') }}" class="btn-sm-smart btn-blue">
                    <i class="fa-solid fa-plus me-1"></i> Add New Branch
                </a>
            </div>
        @endif
    </div>

    {{-- 4. RECENT CASH TRANSFERS (Optional Audit Trail Section) --}}
    @if($recentTransfers->isNotEmpty())
        <div class="transfers-section-wrapper mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-0 fw-bold section-title">
                        <i class="fa-solid fa-clock-rotate-left text-primary-custom me-2"></i>Recent Cash Transfer Activity
                    </h5>
                    <p class="text-muted mb-0 x-small">Latest branch-to-admin cash movements</p>
                </div>
            </div>

            <div class="table-responsive bg-card-container border rounded">
                <table class="table custom-cash-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Amount</th>
                            <th>Before Transfer</th>
                            <th>After Transfer</th>
                            <th>Status</th>
                            <th>Requested By</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransfers as $transfer)
                            <tr>
                                <td class="fw-semibold text-main-theme">
                                    <i class="fa-solid fa-store text-muted me-1 small"></i>
                                    {{ $transfer->branch->name ?? 'N/A' }}
                                </td>
                                <td class="fw-bold text-primary-custom">
                                    ৳ {{ number_format($transfer->amount, 2) }}
                                </td>
                                <td class="text-muted small">৳ {{ number_format($transfer->cash_before_transfer, 2) }}</td>
                                <td class="text-muted small">৳ {{ number_format($transfer->cash_after_transfer, 2) }}</td>
                                <td>
                                    @if($transfer->status === 'approved')
                                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">Approved</span>
                                    @elseif($transfer->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1">Pending</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1">Rejected</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $transfer->user->name ?? 'System' }}</td>
                                <td class="x-small text-muted">{{ $transfer->created_at ? $transfer->created_at->format('d M Y, h:i A') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

{{-- Modern Styles for Cash Dashboard --}}
<style>
    .cash-dashboard-container {
        padding: 0.5rem 0;
    }

    .title-heading {
        color: var(--text-main);
        font-family: 'Inter', sans-serif;
        font-size: 1.6rem;
    }

    .text-main-theme {
        color: var(--text-main) !important;
    }

    .text-primary-custom {
        color: var(--primary) !important;
    }

    .text-success-custom {
        color: #10b981 !important;
    }

    .x-small {
        font-size: 0.78rem;
    }

    .badge-vault-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .live-sync-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .btn-refresh {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--section-bg);
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-refresh:hover {
        color: var(--primary);
        transform: rotate(45deg);
    }

    /* ====================================================
       1. HERO CARD (GRAND COMPANY CASH)
       ==================================================== */
    .hero-cash-card {
        position: relative;
        background: linear-gradient(135deg, #0b134a 0%, #0202e2 50%, #4466ff 100%);
        border-radius: 18px;
        padding: 2.2rem 2rem;
        color: #ffffff;
        box-shadow: 0 12px 30px -8px rgba(2, 2, 226, 0.35);
        overflow: hidden;
    }

    [data-theme="dark"] .hero-cash-card {
        background: linear-gradient(135deg, #0c1222 0%, #1e3a8a 50%, #2563eb 100%);
        box-shadow: 0 12px 30px -8px rgba(0, 0, 0, 0.6);
    }

    .hero-bg-overlay {
        position: absolute;
        top: -40%;
        right: -10%;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .hero-label {
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: rgba(255, 255, 255, 0.8);
        display: block;
        margin-bottom: 0.3rem;
    }

    .hero-amount {
        font-size: 2.8rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 0;
        line-height: 1.1;
        font-family: 'Roboto', 'Inter', sans-serif;
    }

    .hero-amount .currency-symbol {
        font-size: 2.2rem;
        opacity: 0.9;
        font-weight: 600;
    }

    .hero-distribution-box {
        background: rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.12);
        padding: 1rem 1.25rem;
        border-radius: 14px;
        min-width: 280px;
    }

    .ratio-progress-bar {
        height: 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        overflow: hidden;
    }

    .progress-segment.admin-bar {
        background: #38bdf8;
    }

    .progress-segment.branch-bar {
        background: #4ade80;
    }

    .border-white-10 {
        border-color: rgba(255, 255, 255, 0.15) !important;
    }

    /* ====================================================
       2. SECONDARY CARDS (ADMIN CASH & BRANCH SUMMARY)
       ==================================================== */
    .secondary-cash-card {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .secondary-cash-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .icon-bubble {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .admin-icon-bubble {
        background: var(--primary-soft);
        color: var(--primary);
    }

    .branch-icon-bubble {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }

    .card-subtitle-tag {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 0.6px;
        display: block;
    }

    .card-main-title {
        color: var(--text-main);
        font-size: 1.15rem;
        font-weight: 700;
    }

    .sub-amount {
        font-size: 1.9rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .share-pill {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 12px;
    }

    .bg-admin-soft {
        background: var(--primary-soft);
        color: var(--primary);
    }

    .bg-branch-soft {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }

    .mini-stat-tile {
        background: var(--background);
        border: 1px solid var(--border-color);
        padding: 0.6rem 0.8rem;
        border-radius: 10px;
    }

    .top-holding-tile {
        background: var(--background);
        border: 1px dashed var(--border-color);
        padding: 0.6rem 0.8rem;
        border-radius: 10px;
    }

    /* ====================================================
       3. BRANCH CARDS GRID
       ==================================================== */
    .section-title {
        color: var(--text-main);
        font-size: 1.25rem;
    }

    .branch-search-box {
        position: relative;
        min-width: 260px;
        width: 100%;
        max-width: 320px;
    }

    .branch-search-input {
        width: 100%;
        padding: 0.55rem 1rem 0.55rem 2.4rem;
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-main);
        font-size: 0.85rem;
        outline: none;
        transition: border-color 0.2s;
    }

    .branch-search-input:focus {
        border-color: var(--primary);
    }

    .search-icon {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.85rem;
    }

    .branch-cash-card {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.25rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
        position: relative;
    }

    .branch-cash-card:hover {
        transform: translateY(-3px);
        border-color: var(--primary-light);
        box-shadow: 0 8px 22px rgba(2, 2, 226, 0.08);
    }

    .branch-avatar {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .branch-name {
        color: var(--text-main);
        font-size: 0.98rem;
        font-weight: 700;
        max-width: 150px;
    }

    .badge-ratio {
        background: var(--background);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 8px;
    }

    .branch-cash-amount-box {
        background: var(--background);
        padding: 0.85rem 1rem;
        border-radius: 10px;
        border: 1px solid var(--border-color);
    }

    .cash-caption {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        font-weight: 600;
        display: block;
        margin-bottom: 2px;
    }

    .branch-cash-figure {
        color: var(--text-main);
        font-size: 1.45rem;
        font-weight: 800;
        font-family: 'Roboto', 'Inter', sans-serif;
    }

    .branch-cash-figure .curr-sign {
        font-size: 1.15rem;
        color: var(--primary);
        font-weight: 600;
    }

    .branch-progress-track {
        height: 5px;
        border-radius: 4px;
        background: var(--border-color);
        overflow: hidden;
    }

    .branch-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        border-radius: 4px;
    }

    .branch-action-link {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: var(--background);
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .branch-action-link:hover {
        background: var(--primary);
        color: #ffffff;
    }

    /* ====================================================
       4. ACTIVITY TABLE & MISC
       ==================================================== */
    .bg-card-container {
        background: var(--section-bg);
    }

    .custom-cash-table {
        color: var(--text-main);
        font-size: 0.86rem;
    }

    .custom-cash-table thead th {
        background: var(--background);
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
    }

    .custom-cash-table tbody td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .hero-cash-card {
            padding: 1.5rem 1.25rem;
        }
        .hero-amount {
            font-size: 2.1rem;
        }
        .hero-amount .currency-symbol {
            font-size: 1.7rem;
        }
        .hero-distribution-box {
            width: 100%;
            min-width: 100%;
        }
        .sub-amount {
            font-size: 1.5rem;
        }
        .branch-search-box {
            max-width: 100%;
        }
    }
</style>

{{-- Client-side Live Branch Search Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('branchSearchInput');
        const branchCards = document.querySelectorAll('.branch-item-col');
        const noFoundAlert = document.getElementById('noBranchFound');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                let matchCount = 0;

                branchCards.forEach(card => {
                    const name = card.getAttribute('data-name') || '';
                    const manager = card.getAttribute('data-manager') || '';
                    const address = card.getAttribute('data-address') || '';

                    if (name.includes(query) || manager.includes(query) || address.includes(query)) {
                        card.style.display = '';
                        matchCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noFoundAlert) {
                    if (matchCount === 0 && branchCards.length > 0) {
                        noFoundAlert.classList.remove('d-none');
                    } else {
                        noFoundAlert.classList.add('d-none');
                    }
                }
            });
        }
    });
</script>
@endsection
