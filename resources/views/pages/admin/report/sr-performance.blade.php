@extends('layouts.adminlayout')

@section('content')
<style>
  /* ── Page Header ──────────────────────────────────────────── */
  .report-page-header { margin-bottom: 1.5rem; }
  .report-page-header h2 {
    font-size: 24px; font-weight: 700;
    color: var(--text-main); margin-bottom: 4px;
    display: flex; align-items: center; gap: 10px;
  }

  /* ── Filter Card ──────────────────────────────────────────── */
  .filter-panel {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px; padding: 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
  }
  .filter-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 12px; align-items: end;
  }
  .filter-field label {
    display: block; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.5px;
    color: var(--text-muted); margin-bottom: 4px;
  }
  .filter-field .form-control {
    font-size: 13px; padding: 8px 12px; border-radius: 10px;
    border: 1px solid var(--border-color);
    background: var(--section-bg); color: var(--text-main); width: 100%;
  }
  .filter-field .form-control:focus {
    border-color: var(--primary); outline: none;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
  }

  /* ── Summary KPI strip ────────────────────────────────────── */
  .kpi-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1rem; margin-bottom: 1.5rem;
  }
  .kpi-strip-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px; padding: 1rem 1.1rem;
    display: flex; align-items: center; gap: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }
  .kpi-strip-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
  }
  .kpi-strip-info small {
    display: block; font-size: 10px; font-weight: 600;
    text-transform: uppercase; color: var(--text-muted);
  }
  .kpi-strip-info strong {
    font-size: 15px; font-weight: 800; color: var(--text-main);
  }
  .bg-blue-soft   { background: rgba(37,99,235,0.1);   color: #2563eb; }
  .bg-green-soft  { background: rgba(16,185,129,0.1);  color: #10b981; }
  .bg-amber-soft  { background: rgba(245,158,11,0.1);  color: #f59e0b; }
  .bg-purple-soft { background: rgba(139,92,246,0.1);  color: #8b5cf6; }
  .bg-rose-soft   { background: rgba(244,63,94,0.1);   color: #f43f5e; }

  /* ── SR Card Grid ─────────────────────────────────────────── */
  .sr-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.2rem;
    margin-bottom: 2rem;
  }

  .sr-card {
    background: var(--section-bg);
    border: 1.5px solid var(--border-color);
    border-radius: 18px;
    padding: 1.25rem;
    text-decoration: none !important;
    color: var(--text-main) !important;
    display: flex; flex-direction: column; gap: 12px;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    position: relative; overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.03);
  }
  .sr-card:hover {
    transform: translateY(-4px);
    border-color: var(--primary);
    box-shadow: 0 10px 28px rgba(37,99,235,0.12);
  }
  .sr-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #2563eb, #7c3aed);
    border-radius: 18px 18px 0 0;
    opacity: 0; transition: opacity 0.25s;
  }
  .sr-card:hover::before { opacity: 1; }

  /* Rank banner */
  .sr-rank-badge {
    position: absolute; top: 12px; right: 14px;
    font-size: 11px; font-weight: 800;
    padding: 3px 10px; border-radius: 20px;
  }
  .rank-gold   { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
  .rank-silver { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
  .rank-bronze { background: #fff7ed; color: #9a3412; border: 1px solid #fdba74; }
  .rank-normal { background: var(--section-bg); color: var(--text-muted); border: 1px solid var(--border-color); }

  /* Avatar + name row */
  .sr-card-header {
    display: flex; align-items: center; gap: 12px;
  }
  .sr-avatar {
    width: 48px; height: 48px; border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; font-weight: 800; color: #fff;
    flex-shrink: 0; text-transform: uppercase;
  }
  .sr-avatar img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }

  .sr-card-name {
    flex: 1; min-width: 0;
  }
  .sr-card-name h6 {
    margin: 0; font-size: 15px; font-weight: 700;
    color: var(--text-main);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .sr-card-name .branch-tag {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; color: var(--text-muted);
    background: rgba(37,99,235,0.07);
    border-radius: 6px; padding: 2px 7px; margin-top: 3px;
  }

  /* Stats row */
  .sr-stats-row {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
  }
  .sr-stat-box {
    background: var(--body-bg);
    border: 1px solid var(--border-color);
    border-radius: 10px; padding: 8px 6px; text-align: center;
  }
  .sr-stat-box .stat-val {
    font-size: 14px; font-weight: 800; color: var(--text-main);
    display: block; line-height: 1.1;
  }
  .sr-stat-box .stat-lbl {
    font-size: 9px; font-weight: 600; color: var(--text-muted);
    text-transform: uppercase; display: block; margin-top: 2px;
  }

  /* Collection progress bar */
  .collection-bar-wrap { }
  .collection-bar-label {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 10px; color: var(--text-muted); margin-bottom: 4px;
  }
  .collection-bar-label strong { color: var(--text-main); }
  .collection-bar {
    height: 5px; border-radius: 999px;
    background: var(--border-color); overflow: hidden;
  }
  .collection-bar-fill {
    height: 100%; border-radius: 999px;
    background: linear-gradient(90deg, #10b981, #2563eb);
    transition: width 0.6s ease;
  }

  /* Card footer */
  .sr-card-footer {
    display: flex; align-items: center; justify-content: space-between;
    font-size: 11px; color: var(--text-muted);
    padding-top: 8px; border-top: 1px solid var(--border-color);
  }
  .sr-card-footer .view-link {
    font-size: 11px; font-weight: 700; color: var(--primary);
    display: flex; align-items: center; gap: 4px;
  }

  /* Empty state */
  .empty-state {
    text-align: center; padding: 60px 20px;
    color: var(--text-muted);
  }
  .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.3; }

  /* Period selector pills */
  .period-pills { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 1rem; }
  .period-pill {
    padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
    border: 1px solid var(--border-color); background: var(--section-bg);
    color: var(--text-muted); cursor: pointer; text-decoration: none;
    transition: all 0.2s;
  }
  .period-pill:hover, .period-pill.active {
    background: var(--primary); color: #fff; border-color: var(--primary);
  }

  /* ── Search Bar Styling ────────────────────────────────────── */
  .sr-search-box-container {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
  }
  .sr-search-input-group {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
  }
  .sr-search-input-group .search-icon {
    position: absolute;
    left: 14px;
    color: var(--primary);
    font-size: 14px;
    pointer-events: none;
    transition: color 0.2s;
  }
  .sr-search-input {
    width: 100%;
    padding: 10px 38px 10px 40px !important;
    font-size: 13.5px !important;
    border-radius: 12px !important;
    border: 1px solid var(--border-color) !important;
    background: var(--body-bg) !important;
    color: var(--text-main) !important;
    transition: all 0.2s ease;
  }
  .sr-search-input:focus {
    border-color: var(--primary) !important;
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
  }
  .btn-clear-search {
    position: absolute;
    right: 12px;
    background: transparent;
    border: none;
    color: var(--text-muted);
    font-size: 13px;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
  }
  .btn-clear-search:hover {
    color: var(--text-main);
    background: rgba(0,0,0,0.06);
  }
  .search-no-results {
    text-align: center;
    padding: 40px 20px;
    background: var(--section-bg);
    border: 1px dashed var(--border-color);
    border-radius: 16px;
    color: var(--text-muted);
    margin-bottom: 2rem;
  }

  /* Responsive */
  @media (max-width: 576px) {
    .sr-grid { grid-template-columns: 1fr; }
    .kpi-strip { grid-template-columns: 1fr 1fr; }
  }
</style>

<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="report-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h2><i class="fas fa-user-tie text-primary"></i> SR Performance</h2>
      <p class="text-muted mb-0 small">
        Leaderboard of all Field Representatives — ranked by orders. Click any card for full details.
      </p>
    </div>
    <a href="{{ route('admin.report.index') }}" class="btn-smart btn-gray">
      <i class="fas fa-arrow-left me-1"></i> Back
    </a>
  </div>

  {{-- Filter --}}
  <div class="filter-panel">
    <form method="GET" action="{{ route('admin.report.sr-performance') }}">
      <div class="filter-form-grid">
        <div class="filter-field">
          <label><i class="fas fa-calendar-alt me-1 text-primary"></i> From Date</label>
          <input type="date" name="start_date" class="form-control" value="{{ $startDate->toDateString() }}">
        </div>
        <div class="filter-field">
          <label><i class="fas fa-calendar-alt me-1 text-primary"></i> To Date</label>
          <input type="date" name="end_date" class="form-control" value="{{ $endDate->toDateString() }}">
        </div>
        <div class="filter-field d-flex gap-2">
          <button type="submit" class="btn-smart btn-blue w-100">
            <i class="fas fa-sliders me-1"></i> Filter
          </button>
          <a href="{{ route('admin.report.sr-performance') }}" class="btn-smart btn-gray" title="Reset">
            <i class="fas fa-rotate-right"></i>
          </a>
        </div>
      </div>
    </form>
  </div>

  {{-- KPI Strip --}}
  <div class="kpi-strip">
    <div class="kpi-strip-card">
      <div class="kpi-strip-icon bg-blue-soft"><i class="fas fa-users"></i></div>
      <div class="kpi-strip-info">
        <small>Total SRs</small>
        <strong>{{ $srList->count() }}</strong>
      </div>
    </div>
    <div class="kpi-strip-card">
      <div class="kpi-strip-icon bg-purple-soft"><i class="fas fa-receipt"></i></div>
      <div class="kpi-strip-info">
        <small>Total Orders</small>
        <strong>{{ number_format($totalOrders) }}</strong>
      </div>
    </div>
    <div class="kpi-strip-card">
      <div class="kpi-strip-icon bg-blue-soft"><i class="fas fa-sack-dollar"></i></div>
      <div class="kpi-strip-info">
        <small>Net Revenue</small>
        <strong>৳ {{ number_format($totalRevenue, 2) }}</strong>
      </div>
    </div>
    <!-- <div class="kpi-strip-card">
      <div class="kpi-strip-icon bg-green-soft"><i class="fas fa-chart-line"></i></div>
      <div class="kpi-strip-info">
        <small>Gross Profit</small>
        <strong>৳ {{ number_format($totalProfit, 2) }}</strong>
      </div>
    </div> -->
    <div class="kpi-strip-card">
      <div class="kpi-strip-icon bg-amber-soft"><i class="fas fa-hand-holding-dollar"></i></div>
      <div class="kpi-strip-info">
        <small>Cash Collected</small>
        <strong>৳ {{ number_format($totalCollected, 2) }}</strong>
      </div>
    </div>
    <div class="kpi-strip-card">
      <div class="kpi-strip-icon bg-rose-soft"><i class="fas fa-clock"></i></div>
      <div class="kpi-strip-info">
        <small>Outstanding Due</small>
        <strong>৳ {{ number_format($totalDue, 2) }}</strong>
      </div>
    </div>
  </div>

  {{-- Live Search Box --}}
  @if(!$srList->isEmpty())
    <div class="sr-search-box-container">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="sr-search-input-group flex-grow-1" style="max-width: 480px;">
          <i class="fas fa-search search-icon"></i>
          <input type="text" id="srSearchInput" class="form-control sr-search-input" placeholder="Live search by SR name or branch name..." autocomplete="off">
          <button type="button" class="btn-clear-search d-none" id="btnClearSearch" title="Clear Search">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold" id="srCounterBadge" style="font-size: 12px;">
            Showing <span id="visibleSrCount" class="text-primary fw-bold">{{ $srList->count() }}</span> of {{ $srList->count() }} SRs
          </span>
        </div>
      </div>
    </div>
  @endif

  {{-- SR Cards Grid --}}
  @if($srList->isEmpty())
    <div class="empty-state">
      <i class="fas fa-user-slash"></i>
      <p class="fs-5 fw-semibold">No SR accounts found.</p>
      <p class="small">Create SR accounts first to track their performance.</p>
    </div>
  @else
    <div class="sr-grid">
      @foreach($srList as $rank => $row)
        @php
          $rankClass = match($rank) {
            0 => 'rank-gold',
            1 => 'rank-silver',
            2 => 'rank-bronze',
            default => 'rank-normal'
          };
          $rankLabel = match($rank) {
            0 => '🥇 #1',
            1 => '🥈 #2',
            2 => '🥉 #3',
            default => '#' . ($rank + 1)
          };
          $initial = strtoupper(substr($row->sr->fullname, 0, 1));
          $detailUrl = route('admin.report.sr-performance.detail', ['srId' => $row->sr->id, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]);
        @endphp

        <a href="{{ $detailUrl }}" class="sr-card" data-name="{{ strtolower($row->sr->fullname) }}" data-branch="{{ strtolower($row->sr->branch->name ?? 'no branch') }}">

          {{-- Rank badge --}}
          <span class="sr-rank-badge {{ $rankClass }}">{{ $rankLabel }}</span>

          {{-- Header: avatar + name --}}
          <div class="sr-card-header">
            <div class="sr-avatar">
              @if($row->sr->profile_photo_path)
                <img src="{{ asset('storage/' . $row->sr->profile_photo_path) }}" alt="{{ $row->sr->fullname }}">
              @else
                {{ $initial }}
              @endif
            </div>
            <div class="sr-card-name">
              <h6>{{ $row->sr->fullname }}</h6>
              <span class="branch-tag">
                <i class="fas fa-building" style="font-size: 9px;"></i>
                {{ $row->sr->branch->name ?? 'No Branch' }}
              </span>
            </div>
          </div>

          {{-- Stats grid --}}
          <div class="sr-stats-row">
            <div class="sr-stat-box">
              <span class="stat-val">{{ number_format($row->order_count) }}</span>
              <span class="stat-lbl">Orders</span>
            </div>
            <div class="sr-stat-box">
              <span class="stat-val">{{ number_format($row->units_sold) }}</span>
              <span class="stat-lbl">Units</span>
            </div>
            <div class="sr-stat-box">
              <span class="stat-val">{{ $row->active_customers }}</span>
              <span class="stat-lbl">Customers</span>
            </div>
          </div>

          {{-- Revenue & profit --}}
          <div class="sr-stats-row">
            <div class="sr-stat-box" style="grid-column: span 2;">
              <span class="stat-val" style="color: #2563eb; font-size: 13px;">৳ {{ number_format($row->net_revenue, 0) }}</span>
              <span class="stat-lbl">Net Revenue</span>
            </div>
            <div class="sr-stat-box">
              <span class="stat-val" style="color: #15803d;">{{ $row->gross_margin_pct }}%</span>
              <span class="stat-lbl">Margin</span>
            </div>
          </div>

          {{-- Collection bar --}}
          <div class="collection-bar-wrap">
            <div class="collection-bar-label">
              <span>Collection Rate</span>
              <strong>{{ $row->collection_pct }}%</strong>
            </div>
            <div class="collection-bar">
              <div class="collection-bar-fill" style="width: {{ min(100, $row->collection_pct) }}%;"></div>
            </div>
          </div>

          {{-- Footer --}}
          <div class="sr-card-footer">
            <span>
              @if($row->customer_due > 0)
                <span style="color: #b91c1c;"><i class="fas fa-circle-exclamation me-1"></i>৳ {{ number_format($row->customer_due, 0) }} due</span>
              @else
                <span style="color: #15803d;"><i class="fas fa-circle-check me-1"></i>Fully collected</span>
              @endif
            </span>
            <span class="view-link">
              View Details <i class="fas fa-arrow-right"></i>
            </span>
          </div>

        </a>
      @endforeach
    </div>

    {{-- Live Search Empty State --}}
    <div id="noSearchResults" class="search-no-results d-none">
      <i class="fas fa-user-slash fa-2x mb-3 text-muted" style="opacity: 0.5;"></i>
      <h6 class="fw-bold mb-1">No Matching SR Found</h6>
      <p class="small text-muted mb-3">No field representative found matching "<span id="searchedTermText" class="fw-semibold text-dark"></span>"</p>
      <button type="button" class="btn-smart btn-gray btn-sm" onclick="clearSrSearch()">
        <i class="fas fa-rotate-left me-1"></i> Clear Search
      </button>
    </div>
  @endif

</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('srSearchInput');
    const clearBtn = document.getElementById('btnClearSearch');
    const cards = document.querySelectorAll('.sr-card');
    const countEl = document.getElementById('visibleSrCount');
    const noResultsEl = document.getElementById('noSearchResults');
    const searchedTermText = document.getElementById('searchedTermText');

    if (!searchInput) return;

    function performLiveSearch() {
      const query = searchInput.value.trim().toLowerCase();
      let visibleCount = 0;

      if (query.length > 0) {
        clearBtn.classList.remove('d-none');
      } else {
        clearBtn.classList.add('d-none');
      }

      cards.forEach(card => {
        const name = (card.getAttribute('data-name') || '').toLowerCase();
        const branch = (card.getAttribute('data-branch') || '').toLowerCase();

        if (query === '' || name.includes(query) || branch.includes(query)) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (countEl) {
        countEl.textContent = visibleCount;
      }

      if (noResultsEl) {
        if (visibleCount === 0 && query !== '') {
          noResultsEl.classList.remove('d-none');
          if (searchedTermText) searchedTermText.textContent = searchInput.value;
        } else {
          noResultsEl.classList.add('d-none');
        }
      }
    }

    searchInput.addEventListener('input', performLiveSearch);

    // Prevent enter key from submitting any form
    searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
      }
    });

    clearBtn.addEventListener('click', function () {
      searchInput.value = '';
      performLiveSearch();
      searchInput.focus();
    });

    window.clearSrSearch = function () {
      searchInput.value = '';
      performLiveSearch();
      searchInput.focus();
    };
  });
</script>
@endpush
@endsection

