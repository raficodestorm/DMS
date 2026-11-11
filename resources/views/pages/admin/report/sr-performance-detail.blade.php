@extends('layouts.adminlayout')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
  /* ── Page Header ──────────────────────────────────────────── */
  .report-page-header { margin-bottom: 1.5rem; }
  .report-page-header h2 {
    font-size: 22px; font-weight: 700;
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

  /* ── KPI Cards ────────────────────────────────────────────── */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 1rem; margin-bottom: 1.5rem;
  }
  .kpi-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px; padding: 1rem;
    display: flex; align-items: center; gap: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }
  .kpi-icon {
    width: 44px; height: 44px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
  }
  .kpi-info small {
    display: block; font-size: 10px; font-weight: 600;
    text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.3px;
  }
  .kpi-info h4 { margin: 2px 0 0; font-size: 14px; font-weight: 800; color: var(--text-main); }

  .bg-blue-soft   { background: rgba(37,99,235,0.1);  color: #2563eb; }
  .bg-green-soft  { background: rgba(16,185,129,0.1); color: #10b981; }
  .bg-amber-soft  { background: rgba(245,158,11,0.1); color: #f59e0b; }
  .bg-purple-soft { background: rgba(139,92,246,0.1); color: #8b5cf6; }
  .bg-rose-soft   { background: rgba(244,63,94,0.1);  color: #f43f5e; }
  .bg-emerald-soft{ background: rgba(5,150,105,0.1);  color: #059669; }
  .bg-cyan-soft   { background: rgba(6,182,212,0.1);  color: #0891b2; }

  /* ── A4 Document ──────────────────────────────────────────── */
  .report-document-wrapper {
    background: #525659; padding: 30px 15px;
    border-radius: 16px; margin-bottom: 2rem; overflow-x: auto;
  }
  .report-sheet {
    width: 100%; max-width: 860px; margin: 0 auto;
    background: #ffffff !important; color: #0f172a !important;
    padding: 32px 36px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    border-radius: 6px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    position: relative; box-sizing: border-box; line-height: 1.4;
  }
  .report-watermark {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.03; max-width: 380px;
    pointer-events: none; z-index: 0;
  }
  .report-sheet-content { position: relative; z-index: 1; }

  /* Document Header */
  .doc-header-row {
    display: flex; justify-content: space-between; align-items: flex-start;
    border-bottom: 2.5px solid #0f172a;
    padding-bottom: 12px; margin-bottom: 16px;
  }
  .doc-brand img { max-height: 52px; max-width: 170px; object-fit: contain; margin-bottom: 4px; }
  .doc-brand .company-sub { font-size: 11px; color: #475569; font-weight: 600; }
  .doc-title-box { text-align: right; }
  .doc-title-box h1 {
    font-size: 20px; font-weight: 900; color: #0f172a;
    letter-spacing: 0.8px; margin: 0 0 3px 0; text-transform: uppercase;
  }
  .doc-badge-pill {
    display: inline-block; padding: 3px 10px; border-radius: 4px;
    font-size: 10px; font-weight: 700;
    background: #0f172a; color: #fff;
    text-transform: uppercase; letter-spacing: 0.5px;
  }

  /* SR Profile strip in document */
  .doc-sr-profile {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 8px; padding: 12px 14px;
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px;
  }
  .doc-sr-avatar {
    width: 52px; height: 52px; border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #7c3aed);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; font-weight: 800; color: #fff;
    flex-shrink: 0; text-transform: uppercase;
  }
  .doc-sr-avatar img { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; }
  .doc-sr-info h3 { margin: 0 0 2px; font-size: 16px; font-weight: 800; color: #0f172a; }
  .doc-sr-info .meta { font-size: 10.5px; color: #64748b; }
  .doc-meta-bar {
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 8px; padding: 10px 14px;
    display: flex !important; justify-content: space-between !important;
    align-items: center !important; flex-wrap: wrap !important;
    gap: 12px !important; margin-bottom: 18px !important; box-sizing: border-box !important;
  }
  .doc-meta-item { flex: 1 1 auto !important; }
  .doc-meta-item strong { color: #64748b; display: block; font-size: 9.5px; text-transform: uppercase; margin-bottom: 2px; }
  .doc-meta-item span { color: #0f172a; font-weight: 700; font-size: 11.5px; }

  /* Summary boxes */
  .doc-summary-grid {
    display: flex !important; gap: 10px !important;
    width: 100% !important; box-sizing: border-box !important; margin-bottom: 18px !important;
    flex-wrap: wrap !important;
  }
  .doc-summary-box {
    flex: 1 1 calc(25% - 8px) !important; min-width: 110px !important;
    border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px 8px;
    text-align: center; background: #fff; box-sizing: border-box !important;
  }
  .doc-summary-box.primary-stat { background: #f0fdf4 !important; border-color: #86efac !important; }
  .doc-summary-box.info-stat    { background: #eff6ff !important; border-color: #bfdbfe !important; }
  .doc-summary-box small {
    display: block; font-size: 9px; text-transform: uppercase;
    font-weight: 700; color: #64748b; margin-bottom: 2px;
  }
  .doc-summary-box .val { font-size: 14px; font-weight: 800; color: #0f172a; }

  /* Section Title */
  .doc-section-title {
    font-size: 12.5px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.5px; color: #0f172a;
    border-bottom: 1.5px solid #0f172a; padding-bottom: 3px; margin: 22px 0 7px;
    display: flex; justify-content: space-between; align-items: center;
  }

  /* Tables */
  .doc-table { width: 100% !important; border-collapse: collapse !important; font-size: 11px !important; margin-bottom: 15px !important; }
  .doc-table th {
    background: #f1f5f9 !important; color: #1e293b !important;
    font-weight: 800 !important; text-transform: uppercase !important;
    font-size: 9.5px !important; letter-spacing: 0.4px !important;
    border: 1px solid #cbd5e1 !important; padding: 6px 8px !important; text-align: left !important;
  }
  .doc-table td {
    border: 1px solid #e2e8f0 !important; padding: 6px 8px !important;
    color: #1e293b !important; vertical-align: middle !important;
  }
  .doc-table tr:nth-child(even) td { background: #fbfcfe !important; }
  .doc-table tfoot td {
    background: #f1f5f9 !important; font-weight: 800 !important;
    border-top: 2px solid #0f172a !important; border-bottom: 2px solid #0f172a !important;
    color: #0f172a !important; font-size: 11px !important; padding: 6px 8px !important;
  }
  .text-end { text-align: right !important; }
  .text-center { text-align: center !important; }

  /* Status badge */
  .doc-badge {
    display: inline-block; padding: 2px 6px; border-radius: 4px;
    font-size: 9px; font-weight: 700; text-transform: uppercase;
  }
  .badge-approved  { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .badge-pending   { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
  .badge-rejected  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
  .badge-complete  { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
  .badge-delivered { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .badge-other     { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

  /* Signatures */
  .doc-signatures-row {
    display: flex !important; justify-content: space-between !important;
    margin-top: 50px !important; padding-top: 6px !important; box-sizing: border-box !important;
  }
  .doc-sign-col { width: 28% !important; text-align: center !important; }
  .doc-sign-line { border-top: 1.5px dashed #64748b; margin-bottom: 5px; }
  .doc-sign-col p { margin: 0; font-size: 10.5px; font-weight: 700; color: #1e293b; }
  .doc-sign-col small { font-size: 9px; color: #64748b; }
  .doc-audit-footer {
    border-top: 1px solid #e2e8f0; margin-top: 18px; padding-top: 8px;
    display: flex; justify-content: space-between; align-items: center;
    font-size: 9px; color: #64748b;
  }

  /* Page-break management */
  .doc-signatures-row, .doc-audit-footer, .doc-summary-grid,
  .doc-meta-bar, .doc-header-row, .doc-sr-profile {
    page-break-inside: avoid !important; break-inside: avoid !important;
  }
  .pdf-section { page-break-inside: avoid !important; break-inside: avoid !important; }

  /* ── Print ────────────────────────────────────────────────── */
  @media print {
    @page { size: A4 portrait; margin: 8mm 6mm; }
    body { background: #fff !important; margin: 0; padding: 0; }
    aside, .sidebar, .custom-navbar, .navbar, .top-header,
    .sidebar-overlay, .sidebar-overlay *, header, #overlay,
    .report-page-header, .filter-panel, .kpi-grid,
    .btn-smart, .theme-toggle, .notification-wrapper, .profile-wrapper, footer {
      display: none !important; visibility: hidden !important;
    }
    body, html { margin: 0 !important; padding: 0 !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
    .report-document-wrapper { background: transparent !important; padding: 0 !important; margin: 0 !important; box-shadow: none !important; border-radius: 0 !important; overflow: visible !important; }
    .report-sheet { max-width: 100% !important; width: 100% !important; box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; margin: 0 !important; }
    .doc-table { page-break-inside: auto; }
    .doc-table tr { page-break-inside: avoid; }
    .doc-table thead { display: table-header-group; }
    .doc-table tfoot { display: table-footer-group; }
  }

  /* ── Responsive ───────────────────────────────────────────── */
  .doc-table-responsive { width: 100%; }
  @media (max-width: 768px) {
    .doc-summary-box { flex: 1 1 calc(50% - 5px) !important; }
    .report-sheet { padding: 20px 12px; }
    .doc-table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; margin-bottom: 12px; }
    .doc-table-compact { font-size: 9px !important; line-height: 1.25 !important; }
    .doc-table-compact th { font-size: 8px !important; padding: 5px 4px !important; }
    .doc-table-compact td { font-size: 9px !important; padding: 5px 4px !important; }
    .doc-table-compact tfoot td { font-size: 9px !important; padding: 5px 4px !important; }
  }
  @media (max-width: 480px) {
    .report-sheet { padding: 16px 8px; }
    .doc-table-compact { font-size: 8px !important; }
    .doc-table-compact th { font-size: 7px !important; padding: 3px 2px !important; }
    .doc-table-compact td { font-size: 8px !important; padding: 3px 2px !important; }
  }
</style>

<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="report-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h2>
        <i class="fas fa-user-tie text-primary"></i>
        {{ $sr->fullname }}
        <span class="fs-6 fw-normal text-muted">— SR Performance Detail</span>
      </h2>
      <p class="text-muted mb-0 small">
        <i class="fas fa-building me-1"></i>{{ $sr->branch->name ?? 'No Branch' }} &nbsp;|&nbsp;
        {{ $startDate->format('d M Y') }} — {{ $endDate->format('d M Y') }}
        ({{ $periodDays }} day{{ $periodDays > 1 ? 's' : '' }})
      </p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a href="{{ route('admin.report.sr-performance', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn-smart btn-gray">
        <i class="fas fa-arrow-left me-1"></i> Back to SR List
      </a>
      <button type="button" class="btn-smart btn-blue" onclick="window.print()">
        <i class="fas fa-print me-1"></i> Print
      </button>
      <button type="button" class="btn-smart btn-green" id="btnDownloadPdf" onclick="downloadA4PDF()">
        <i class="fas fa-file-pdf me-1"></i> Download PDF
      </button>
    </div>
  </div>

  {{-- Date Filter --}}
  <div class="filter-panel">
    <form method="GET" action="{{ route('admin.report.sr-performance.detail', $sr->id) }}">
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
            <i class="fas fa-sliders me-1"></i> Apply
          </button>
          <a href="{{ route('admin.report.sr-performance.detail', $sr->id) }}" class="btn-smart btn-gray" title="Reset">
            <i class="fas fa-rotate-right"></i>
          </a>
        </div>
      </div>
    </form>
  </div>

  {{-- Screen KPI Cards --}}
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-icon bg-purple-soft"><i class="fas fa-receipt"></i></div>
      <div class="kpi-info"><small>Total Orders</small><h4>{{ number_format($orderCount) }}</h4></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon bg-blue-soft"><i class="fas fa-boxes-packing"></i></div>
      <div class="kpi-info"><small>Units Sold</small><h4>{{ number_format($unitsSold) }}</h4></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon bg-blue-soft"><i class="fas fa-sack-dollar"></i></div>
      <div class="kpi-info"><small>Net Revenue</small><h4>৳ {{ number_format($netRevenue, 2) }}</h4></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon bg-emerald-soft"><i class="fas fa-chart-line"></i></div>
      <div class="kpi-info">
        <small>Gross Profit ({{ $grossMarginPct }}%)</small>
        <h4 style="color: #059669;">৳ {{ number_format($netProfit, 2) }}</h4>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon bg-amber-soft"><i class="fas fa-hand-holding-dollar"></i></div>
      <div class="kpi-info">
        <small>Collected ({{ $collectionPct }}%)</small>
        <h4>৳ {{ number_format($cashCollected, 2) }}</h4>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon {{ $customerDue > 0 ? 'bg-rose-soft' : 'bg-green-soft' }}"><i class="fas fa-clock"></i></div>
      <div class="kpi-info">
        <small>Outstanding Due</small>
        <h4 class="{{ $customerDue > 0 ? 'text-danger' : 'text-success' }}">৳ {{ number_format($customerDue, 2) }}</h4>
      </div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon bg-cyan-soft"><i class="fas fa-users"></i></div>
      <div class="kpi-info"><small>Active Customers</small><h4>{{ $activeCustomers }}</h4></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon bg-purple-soft"><i class="fas fa-shopping-bag"></i></div>
      <div class="kpi-info"><small>Avg Order Value</small><h4>৳ {{ number_format($avgOrderValue, 2) }}</h4></div>
    </div>
  </div>

  {{-- A4 Report Document --}}
  <div class="report-document-wrapper">
    <div class="report-sheet" id="reportPrintArea">

      <img src="{{ asset('image/relectric-logo.png') }}" class="report-watermark" alt="Watermark">

      <div class="report-sheet-content">

        {{-- Doc Header --}}
        <div class="doc-header-row">
          <div class="doc-brand">
            <img src="{{ asset('image/relectric-logo.png') }}" alt="R-ELECTRIC">
            <div class="company-sub">Dealership &amp; Inventory Management System</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">Multi-Brand Authorized Electric &amp; Electronics Distribution</div>
          </div>
          <div class="doc-title-box">
            <h1>SR Performance Report</h1>
            <div class="doc-badge-pill">Individual Field Representative Analytics</div>
          </div>
        </div>

        {{-- SR Profile Strip --}}
        <div class="doc-sr-profile">
          <div class="doc-sr-avatar">
            @if($sr->profile_photo_path)
              <img src="{{ asset('storage/' . $sr->profile_photo_path) }}" alt="{{ $sr->fullname }}">
            @else
              {{ strtoupper(substr($sr->fullname, 0, 1)) }}
            @endif
          </div>
          <div class="doc-sr-info" style="flex: 1;">
            <h3>{{ $sr->fullname }}</h3>
            <div class="meta">
              <span style="margin-right: 14px;"><strong>Username:</strong> {{ $sr->username }}</span>
              <span style="margin-right: 14px;"><strong>Branch:</strong> {{ $sr->branch->name ?? 'N/A' }}</span>
              <span><strong>Status:</strong> {{ ucfirst($sr->status) }}</span>
            </div>
          </div>
          <div style="text-align: right; font-size: 10px; color: #64748b;">
            <strong>Registered Customers:</strong> {{ $allCustomers->count() }}<br>
            <strong>Report Period:</strong> {{ $periodDays }} day(s)
          </div>
        </div>

        {{-- Metadata Bar --}}
        <div class="doc-meta-bar">
          <div class="doc-meta-item">
            <strong>Report Period</strong>
            <span>{{ $startDate->format('d M, Y') }} — {{ $endDate->format('d M, Y') }}</span>
          </div>
          <div class="doc-meta-item">
            <strong>Assigned Branch</strong>
            <span>{{ $sr->branch->name ?? 'N/A' }}</span>
          </div>
          <div class="doc-meta-item">
            <strong>Total Orders</strong>
            <span>{{ number_format($orderCount) }} orders</span>
          </div>
          <div class="doc-meta-item" style="text-align: right;">
            <strong>Generated On &amp; By</strong>
            <span>{{ now()->format('d M, Y h:i A') }} ({{ auth()->user()->fullname ?? 'Admin' }})</span>
          </div>
        </div>

        {{-- Summary Boxes --}}
        <div class="doc-summary-grid">
          <div class="doc-summary-box info-stat">
            <small>Net Revenue</small>
            <div class="val" style="color: #1e3a8a;">৳ {{ number_format($netRevenue, 2) }}</div>
          </div>
          <div class="doc-summary-box primary-stat">
            <small>Gross Profit ({{ $grossMarginPct }}%)</small>
            <div class="val" style="color: #15803d;">৳ {{ number_format($netProfit, 2) }}</div>
          </div>
          <div class="doc-summary-box">
            <small>Cash Collected ({{ $collectionPct }}%)</small>
            <div class="val" style="color: #0369a1;">৳ {{ number_format($cashCollected, 2) }}</div>
          </div>
          <div class="doc-summary-box {{ $customerDue > 0 ? '' : 'primary-stat' }}" style="{{ $customerDue > 0 ? 'background:#fff1f2!important;border-color:#fecdd3!important;' : '' }}">
            <small>Outstanding Due</small>
            <div class="val" style="color: {{ $customerDue > 0 ? '#b91c1c' : '#15803d' }};">৳ {{ number_format($customerDue, 2) }}</div>
          </div>
        </div>

        {{-- Section 1: Order Summary by Status --}}
        <div>
          <div class="doc-section-title">
            <span>1. Order Summary &amp; Status Breakdown</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th class="text-end">Value</th>
                  <th>Metric</th>
                  <th class="text-end">Value</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Total Orders Placed</td>
                  <td class="text-end fw-bold">{{ number_format($orderCount) }}</td>
                  <td>Units Sold</td>
                  <td class="text-end fw-bold">{{ number_format($unitsSold) }}</td>
                </tr>
                <tr>
                  <td>Gross Invoice Sales</td>
                  <td class="text-end">৳ {{ number_format($netSales + $totalDiscounts, 2) }}</td>
                  <td>Total Discounts Given</td>
                  <td class="text-end" style="color: #b91c1c;">- ৳ {{ number_format($totalDiscounts, 2) }}</td>
                </tr>
                <tr>
                  <td>Net Invoiced Sales</td>
                  <td class="text-end">৳ {{ number_format($netSales, 2) }}</td>
                  <td>Customer Returns</td>
                  <td class="text-end" style="color: #b91c1c;">- ৳ {{ number_format($returnAmount, 2) }} ({{ $returnCount }}x)</td>
                </tr>
                <tr style="background: #eff6ff;">
                  <td><strong>Net Realized Revenue</strong></td>
                  <td class="text-end fw-bold" style="color: #1e3a8a;">৳ {{ number_format($netRevenue, 2) }}</td>
                  <td>Avg Order Value</td>
                  <td class="text-end">৳ {{ number_format($avgOrderValue, 2) }}</td>
                </tr>
                <tr>
                  <td>Gross Profit</td>
                  <td class="text-end fw-bold" style="color: #15803d;">৳ {{ number_format($netProfit, 2) }} ({{ $grossMarginPct }}%)</td>
                  <td>Active Customers (This Period)</td>
                  <td class="text-end">{{ $activeCustomers }}</td>
                </tr>
                <tr>
                  <td>Cash Collected</td>
                  <td class="text-end fw-bold" style="color: #0369a1;">৳ {{ number_format($cashCollected, 2) }} ({{ $collectionPct }}%)</td>
                  <td>Outstanding Customer Due</td>
                  <td class="text-end" style="color: {{ $customerDue > 0 ? '#b91c1c' : '#15803d' }};">৳ {{ number_format($customerDue, 2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        {{-- Section 2: Customer-wise Revenue Breakdown --}}
        @if($customerBreakdown->count() > 0)
        <div>
          <div class="doc-section-title">
            <span>2. Customer-Wise Revenue &amp; Collection Breakdown</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 25px;" class="text-center">#</th>
                  <th>Customer / Shop</th>
                  <th class="text-end" style="width: 90px;">Orders</th>
                  <th class="text-end" style="width: 130px;">Net Sales</th>
                  <th class="text-end" style="width: 130px;">Cash Collected</th>
                  <th class="text-end" style="width: 110px;">Due</th>
                </tr>
              </thead>
              <tbody>
                @foreach($customerBreakdown as $ci => $cRow)
                  @php
                    $cDue = max(0, $cRow->net_sales - $cRow->cash_collected);
                  @endphp
                  <tr>
                    <td class="text-center">{{ $ci + 1 }}</td>
                    <td>
                      <strong>{{ $cRow->customer->shop_name ?? 'Unknown' }}</strong>
                      @if($cRow->customer?->manager)
                        <span style="display:block;font-size:9px;color:#64748b;">{{ $cRow->customer->manager }}</span>
                      @endif
                    </td>
                    <td class="text-end">{{ $cRow->order_count }}</td>
                    <td class="text-end">৳ {{ number_format($cRow->net_sales, 2) }}</td>
                    <td class="text-end" style="color: #15803d;">৳ {{ number_format($cRow->cash_collected, 2) }}</td>
                    <td class="text-end" style="color: {{ $cDue > 0 ? '#b91c1c' : '#15803d' }};">৳ {{ number_format($cDue, 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">TOTAL:</td>
                  <td class="text-end">{{ $orderCount }}</td>
                  <td class="text-end">৳ {{ number_format($netSales, 2) }}</td>
                  <td class="text-end" style="color: #15803d;">৳ {{ number_format($cashCollected, 2) }}</td>
                  <td class="text-end" style="color: {{ $customerDue > 0 ? '#b91c1c' : '#15803d' }};">৳ {{ number_format($customerDue, 2) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        @endif

        {{-- Section 3: Top Products Sold --}}
        @if($topProducts->count() > 0)
        <div>
          <div class="doc-section-title">
            <span>3. Top Products Sold (By Quantity)</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 25px;" class="text-center">#</th>
                  <th>Product</th>
                  <th style="width: 80px;" class="text-center">SKU</th>
                  <th class="text-end" style="width: 90px;">Qty Sold</th>
                  <th class="text-end" style="width: 125px;">Total Sales</th>
                  <th class="text-end" style="width: 110px;">Profit</th>
                </tr>
              </thead>
              <tbody>
                @foreach($topProducts as $pi => $pRow)
                  <tr>
                    <td class="text-center">{{ $pi + 1 }}</td>
                    <td><strong>{{ $pRow->product_name }}</strong></td>
                    <td class="text-center" style="font-size: 9px; color: #64748b; font-family: monospace;">{{ $pRow->sku }}</td>
                    <td class="text-end fw-bold">{{ number_format($pRow->total_qty) }}</td>
                    <td class="text-end">৳ {{ number_format($pRow->total_sales, 2) }}</td>
                    <td class="text-end" style="color: #15803d;">৳ {{ number_format($pRow->total_profit, 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-end">TOP {{ $topProducts->count() }} PRODUCTS TOTAL:</td>
                  <td class="text-end">{{ number_format($topProducts->sum('total_qty')) }}</td>
                  <td class="text-end">৳ {{ number_format($topProducts->sum('total_sales'), 2) }}</td>
                  <td class="text-end" style="color: #15803d;">৳ {{ number_format($topProducts->sum('total_profit'), 2) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        @endif

        {{-- Signatures --}}
        <div class="doc-signatures-row pdf-section">
          <div class="doc-sign-col">
            <div class="doc-sign-line"></div>
            <p>Prepared By</p>
            <small>{{ auth()->user()->fullname ?? 'System Admin' }}</small>
          </div>
          <div class="doc-sign-col">
            <div class="doc-sign-line"></div>
            <p>Field Representative</p>
            <small>{{ $sr->fullname }}</small>
          </div>
          <div class="doc-sign-col">
            <div class="doc-sign-line"></div>
            <p>Authorized Signature</p>
            <small>Managing Director / Executive Board</small>
          </div>
        </div>

        {{-- Audit Footer --}}
        <div class="doc-audit-footer pdf-section">
          <div>Generated via <strong>R-ELECTRIC DMS Reporting Engine</strong> (Automated &amp; Certified)</div>
          <div>Report Code: <span style="font-family: monospace;">SR-{{ $sr->id }}-{{ $startDate->format('ymd') }}-{{ $endDate->format('ymd') }}</span></div>
        </div>

      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  function downloadA4PDF() {
    const element = document.getElementById('reportPrintArea');
    const btn = document.getElementById('btnDownloadPdf');
    const originalBtnHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating PDF...';
    btn.disabled = true;

    const opt = {
      margin:       [6, 6, 6, 6],
      filename:     'SR_Performance_{{ Str::slug($sr->fullname) }}_{{ $startDate->format("Ymd") }}_{{ $endDate->format("Ymd") }}.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2, useCORS: true, logging: false, letterRendering: true, scrollY: 0 },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
      pagebreak:    {
        mode: 'legacy',
        avoid: ['.doc-signatures-row', '.doc-audit-footer', '.doc-summary-grid', '.doc-meta-bar', '.doc-header-row', '.doc-sr-profile', 'tfoot']
      }
    };

    html2pdf().set(opt).from(element).save().then(() => {
      btn.innerHTML = originalBtnHtml;
      btn.disabled = false;
    }).catch(err => {
      console.error('PDF error:', err);
      btn.innerHTML = originalBtnHtml;
      btn.disabled = false;
      window.print();
    });
  }
</script>
@endpush

