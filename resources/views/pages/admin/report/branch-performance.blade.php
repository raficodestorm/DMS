@extends('layouts.adminlayout')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
  /* ── Screen Page Layout ─────────────────────────────────────────── */
  .report-page-header {
    margin-bottom: 1.5rem;
  }
  .report-page-header h2 {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* ── Filter Card ────────────────────────────────────────────────── */
  .filter-panel {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
  }

  .filter-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap: 12px;
    align-items: end;
  }

  .filter-field label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    margin-bottom: 4px;
  }

  .filter-field .form-control,
  .filter-field .form-select {
    font-size: 13px;
    padding: 8px 12px;
    border-radius: 10px;
    border: 1px solid var(--border-color);
    background: var(--section-bg);
    color: var(--text-main);
    width: 100%;
  }

  .filter-field .form-control:focus,
  .filter-field .form-select:focus {
    border-color: var(--primary);
    outline: none;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
  }

  /* ── KPI Highlight Cards (Screen only) ────────────────────────── */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .kpi-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 1.1rem;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
  }

  .kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
  }

  .kpi-info small {
    display: block;
    font-size: 10px;
    font-weight: 500;
    text-transform: uppercase;
    color: var(--text-muted);
    letter-spacing: 0.3px;
  }

  .kpi-info h4 {
    margin: 2px 0 0;
    font-size: 15px;
    font-weight: 700;
    color: var(--text-main);
  }

  .bg-blue-soft    { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
  .bg-green-soft   { background: rgba(16, 185, 129, 0.1); color: #10b981; }
  .bg-amber-soft   { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
  .bg-purple-soft  { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
  .bg-rose-soft    { background: rgba(244, 63, 94, 0.1); color: #f43f5e; }
  .bg-emerald-soft { background: rgba(5, 150, 105, 0.1); color: #059669; }
  .bg-cyan-soft    { background: rgba(6, 182, 212, 0.1); color: #0891b2; }

  /* ── A4 Report Document Styling ─────────────────────────────────── */
  .report-document-wrapper {
    background: #525659;
    padding: 30px 15px;
    border-radius: 16px;
    margin-bottom: 2rem;
    overflow-x: auto;
  }

  .report-sheet {
    width: 100%;
    max-width: 860px;
    margin: 0 auto;
    background: #ffffff !important;
    color: #0f172a !important;
    padding: 32px 36px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border-radius: 6px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    position: relative;
    box-sizing: border-box;
    line-height: 1.4;
  }

  /* Watermark */
  .report-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.03;
    max-width: 380px;
    pointer-events: none;
    z-index: 0;
  }

  .report-sheet-content {
    position: relative;
    z-index: 1;
  }

  /* Document Header */
  .doc-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 2.5px solid #0f172a;
    padding-bottom: 12px;
    margin-bottom: 16px;
  }

  .doc-brand img {
    max-height: 52px;
    max-width: 170px;
    object-fit: contain;
    margin-bottom: 4px;
  }

  .doc-brand .company-sub {
    font-size: 11px;
    color: #475569;
    font-weight: 600;
  }

  .doc-title-box {
    text-align: right;
  }

  .doc-title-box h1 {
    font-size: 21px;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: 0.8px;
    margin: 0 0 3px 0;
    text-transform: uppercase;
  }

  .doc-badge-pill {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    background: #0f172a;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* Document Metadata Bar */
  .doc-meta-bar {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    flex-wrap: wrap !important;
    gap: 12px !important;
    margin-bottom: 18px !important;
    box-sizing: border-box !important;
  }

  .doc-meta-item {
    flex: 1 1 auto !important;
  }

  .doc-meta-item strong {
    color: #64748b;
    display: block;
    font-size: 9.5px;
    text-transform: uppercase;
    margin-bottom: 2px;
  }

  .doc-meta-item span {
    color: #0f172a;
    font-weight: 700;
    font-size: 11.5px;
  }

  /* Executive Summary Grid (4-box) */
  .doc-summary-grid {
    display: flex !important;
    gap: 12px !important;
    width: 100% !important;
    box-sizing: border-box !important;
    margin-bottom: 18px !important;
  }

  .doc-summary-box {
    flex: 1 1 0 !important;
    min-width: 0 !important;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 11px 10px;
    text-align: center;
    background: #ffffff;
    box-sizing: border-box !important;
  }

  .doc-summary-box.primary-stat {
    background: #f0fdf4 !important;
    border-color: #86efac !important;
  }

  .doc-summary-box.info-stat {
    background: #eff6ff !important;
    border-color: #bfdbfe !important;
  }

  .doc-summary-box small {
    display: block;
    font-size: 9.5px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 2px;
  }

  .doc-summary-box .val {
    font-size: 15.5px;
    font-weight: 800;
    color: #0f172a;
  }

  /* Section Title inside Document */
  .doc-section-title {
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #0f172a;
    border-bottom: 1.5px solid #0f172a;
    padding-bottom: 3px;
    margin: 25px 0 7px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  /* Document Data Tables */
  .doc-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 11px !important;
    margin-bottom: 15px !important;
  }

  .doc-table th {
    background: #f1f5f9 !important;
    color: #1e293b !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    font-size: 9.5px !important;
    letter-spacing: 0.4px !important;
    border: 1px solid #cbd5e1 !important;
    padding: 6px 8px !important;
    text-align: left !important;
  }

  .doc-table td {
    border: 1px solid #e2e8f0 !important;
    padding: 6px 8px !important;
    color: #1e293b !important;
    vertical-align: middle !important;
  }

  .doc-table tr:nth-child(even) td {
    background: #fbfcfe !important;
  }

  .doc-table tfoot td {
    background: #f1f5f9 !important;
    font-weight: 800 !important;
    border-top: 2px solid #0f172a !important;
    border-bottom: 2px solid #0f172a !important;
    color: #0f172a !important;
    font-size: 11px !important;
    padding: 6px 8px !important;
  }

  .text-end { text-align: right !important; }
  .text-center { text-align: center !important; }

  /* Rank badge */
  .rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 10px;
    font-weight: 800;
    background: #e2e8f0;
    color: #475569;
  }
  .rank-badge.rank-1 { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
  .rank-badge.rank-2 { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
  .rank-badge.rank-3 { background: #fff7ed; color: #9a3412; border: 1px solid #fdba74; }

  /* Status Badges */
  .doc-badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
  }
  .doc-badge-profit { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .doc-badge-loss   { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
  .doc-badge-warn   { background: #fff7ed; color: #c2410c; border: 1px solid #fdba74; }
  .doc-badge-info   { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

  /* Document Footer & Signatures */
  .doc-signatures-row {
    display: flex !important;
    justify-content: space-between !important;
    margin-top: 50px !important;
    padding-top: 6px !important;
    box-sizing: border-box !important;
  }

  .doc-sign-col {
    width: 28% !important;
    text-align: center !important;
  }

  .doc-sign-line {
    border-top: 1.5px dashed #64748b;
    margin-bottom: 5px;
  }

  .doc-sign-col p {
    margin: 0;
    font-size: 10.5px;
    font-weight: 700;
    color: #1e293b;
  }

  .doc-sign-col small {
    font-size: 9px;
    color: #64748b;
  }

  .doc-audit-footer {
    border-top: 1px solid #e2e8f0;
    margin-top: 18px;
    padding-top: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 9px;
    color: #64748b;
  }

  /* Page-break management for PDF */
  .doc-signatures-row,
  .doc-audit-footer,
  .doc-summary-grid,
  .doc-meta-bar,
  .doc-header-row {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }

  .pdf-section {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }

  /* ── A4 Print Formatting ─────────────────────────────────────────── */
  @media print {
    @page {
      size: A4 portrait;
      margin: 8mm 6mm;
    }

    body {
      background: #ffffff !important;
      color: #000000 !important;
      margin: 0;
      padding: 0;
    }

    aside,
    .sidebar,
    .custom-navbar,
    .navbar,
    .top-header,
    .sidebar-overlay,
    .sidebar-overlay *,
    header,
    #overlay,
    .report-page-header,
    .filter-panel,
    .kpi-grid,
    .btn-smart,
    .theme-toggle,
    .notification-wrapper,
    .profile-wrapper,
    footer {
      display: none !important;
      visibility: hidden !important;
    }

    body, html {
      margin: 0 !important;
      padding: 0 !important;
    }

    .main-content {
      margin-left: 0 !important;
      padding-left: 0 !important;
      padding-top: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
    }

    .report-document-wrapper {
      background: transparent !important;
      padding: 0 !important;
      margin: 0 !important;
      box-shadow: none !important;
      border-radius: 0 !important;
      overflow: visible !important;
    }

    .report-sheet {
      max-width: 100% !important;
      width: 100% !important;
      box-shadow: none !important;
      border-radius: 0 !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    .doc-table {
      page-break-inside: auto;
    }
    .doc-table tr {
      page-break-inside: avoid;
    }
    .doc-table thead {
      display: table-header-group;
    }
    .doc-table tfoot {
      display: table-footer-group;
    }
    .doc-signatures-row {
      page-break-inside: avoid;
    }
  }

  /* Responsive tweaks */
  .doc-table-responsive {
    width: 100%;
  }

  @media (max-width: 768px) {
    .doc-summary-grid {
      flex-wrap: wrap;
    }
    .doc-summary-box {
      flex: 1 1 calc(50% - 6px) !important;
    }
    .report-sheet {
      padding: 20px 12px;
    }

    .doc-table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      margin-bottom: 12px;
    }

    .doc-table-compact {
      font-size: 9px !important;
      line-height: 1.25 !important;
      margin-bottom: 8px !important;
    }

    .doc-table-compact th {
      font-size: 8px !important;
      padding: 5px 4px !important;
      letter-spacing: 0 !important;
      white-space: normal !important;
    }

    .doc-table-compact td {
      font-size: 9px !important;
      padding: 5px 4px !important;
      line-height: 1.2 !important;
    }

    .doc-table-compact tfoot td {
      font-size: 9px !important;
      padding: 5px 4px !important;
    }

    .doc-table-compact .text-end {
      white-space: nowrap !important;
    }
  }

  @media (max-width: 480px) {
    .report-sheet {
      padding: 16px 8px;
    }
    .doc-table-compact {
      font-size: 8px !important;
    }
    .doc-table-compact th {
      font-size: 7px !important;
      padding: 3px 2px !important;
    }
    .doc-table-compact td {
      font-size: 8px !important;
      padding: 3px 2px !important;
    }
    .doc-table-compact tfoot td {
      font-size: 8px !important;
      padding: 4px 2px !important;
    }
  }
</style>

<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="report-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h2>
        <i class="fas fa-code-branch text-primary"></i> Branch Performance Report
      </h2>
      <p class="text-muted mb-0 small">
        Side-by-side performance comparison across all branches: revenue, profit, OPEX, stock, and collection analytics.
      </p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a href="{{ route('admin.report.index') }}" class="btn-smart btn-gray">
        <i class="fas fa-arrow-left me-1"></i> Back
      </a>
      <button type="button" class="btn-smart btn-blue" onclick="window.print()">
        <i class="fas fa-print me-1"></i> Print Report
      </button>
      <button type="button" class="btn-smart btn-green" id="btnDownloadPdf" onclick="downloadA4PDF()">
        <i class="fas fa-file-pdf me-1"></i> Download PDF
      </button>
    </div>
  </div>

  {{-- Filter Panel --}}
  <div class="filter-panel">
    <form method="GET" action="{{ route('admin.report.branch-performance') }}" id="branchPerfFilterForm">
      <div class="filter-form-grid">
        {{-- Start Date --}}
        <div class="filter-field">
          <label><i class="fas fa-calendar-alt me-1 text-primary"></i> From Date</label>
          <input type="date" name="start_date" class="form-control" value="{{ $startDate->toDateString() }}">
        </div>

        {{-- End Date --}}
        <div class="filter-field">
          <label><i class="fas fa-calendar-alt me-1 text-primary"></i> To Date</label>
          <input type="date" name="end_date" class="form-control" value="{{ $endDate->toDateString() }}">
        </div>

        {{-- Action Buttons --}}
        <div class="filter-field d-flex gap-2">
          <button type="submit" class="btn-smart btn-blue w-100">
            <i class="fas fa-sliders me-1"></i> Generate
          </button>
          <a href="{{ route('admin.report.branch-performance') }}" class="btn-smart btn-gray" title="Reset Filters">
            <i class="fas fa-rotate-right"></i>
          </a>
        </div>
      </div>
    </form>
  </div>

  {{-- Screen KPI Stats Grid --}}
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-icon bg-blue-soft">
        <i class="fas fa-sack-dollar"></i>
      </div>
      <div class="kpi-info">
        <small>Company Net Revenue</small>
        <h4>৳ {{ number_format($companyRevenue, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-emerald-soft">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="kpi-info">
        <small>Gross Profit ({{ $companyGrossMarginPct }}%)</small>
        <h4 style="color: #059669;">৳ {{ number_format($companyGrossProfit, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-amber-soft">
        <i class="fas fa-money-bill-wave"></i>
      </div>
      <div class="kpi-info">
        <small>Total Branch OPEX</small>
        <h4 class="text-warning-emphasis">৳ {{ number_format($companyOpex, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon {{ $companyNetProfit >= 0 ? 'bg-green-soft' : 'bg-rose-soft' }}">
        <i class="fas fa-scale-balanced"></i>
      </div>
      <div class="kpi-info">
        <small>Net Contribution ({{ $companyNetMarginPct }}%)</small>
        <h4 class="{{ $companyNetProfit >= 0 ? 'text-success' : 'text-danger' }}">
          ৳ {{ number_format($companyNetProfit, 2) }}
        </h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-purple-soft">
        <i class="fas fa-receipt"></i>
      </div>
      <div class="kpi-info">
        <small>Total Orders</small>
        <h4>{{ number_format($companyOrders) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-cyan-soft">
        <i class="fas fa-boxes-stacked"></i>
      </div>
      <div class="kpi-info">
        <small>Stock Cost Valuation</small>
        <h4>৳ {{ number_format($companyStockCostVal, 2) }}</h4>
      </div>
    </div>
  </div>

  {{-- A4 Printable / Exportable Document --}}
  <div class="report-document-wrapper">
    <div class="report-sheet" id="reportPrintArea">

      {{-- Watermark --}}
      <img src="{{ asset('image/relectric-logo.png') }}" class="report-watermark" alt="Watermark">

      <div class="report-sheet-content">

        {{-- Document Header --}}
        <div class="doc-header-row">
          <div class="doc-brand">
            <img src="{{ asset('image/relectric-logo.png') }}" alt="R-ELECTRIC" class="img-fluid">
            <div class="company-sub">Dealership &amp; Inventory Management System</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
              Multi-Brand Authorized Electric &amp; Electronics Distribution
            </div>
          </div>
          <div class="doc-title-box">
            <h1>Branch Performance Report</h1>
            <div class="doc-badge-pill">Multi-Branch Comparative Analytics</div>
          </div>
        </div>

        {{-- Document Metadata Bar --}}
        <div class="doc-meta-bar">
          <div class="doc-meta-item">
            <strong>Report Period</strong>
            <span>{{ $startDate->format('d M, Y') }} — {{ $endDate->format('d M, Y') }}</span>
          </div>
          <div class="doc-meta-item">
            <strong>Coverage</strong>
            <span>All Branches (Consolidated)</span>
          </div>
          <div class="doc-meta-item">
            <strong>Reporting Period</strong>
            <span>{{ $periodDays }} Day(s)</span>
          </div>
          <div class="doc-meta-item" style="text-align: right;">
            <strong>Generated On &amp; By</strong>
            <span>{{ now()->format('d M, Y h:i A') }} ({{ auth()->user()->fullname ?? 'Admin' }})</span>
          </div>
        </div>

        {{-- Executive Summary Boxes --}}
        <div class="doc-summary-grid">
          <div class="doc-summary-box info-stat">
            <small>Company Net Revenue</small>
            <div class="val" style="color: #1e3a8a;">৳ {{ number_format($companyRevenue, 2) }}</div>
          </div>
          <div class="doc-summary-box primary-stat">
            <small>Gross Trading Profit</small>
            <div class="val" style="color: #15803d;">
              ৳ {{ number_format($companyGrossProfit, 2) }}
              <span style="font-size: 11px; font-weight: 600;">({{ $companyGrossMarginPct }}%)</span>
            </div>
          </div>
          <div class="doc-summary-box">
            <small>Total Branch OPEX</small>
            <div class="val" style="color: #b45309;">৳ {{ number_format($companyOpex, 2) }}</div>
          </div>
          <div class="doc-summary-box {{ $companyNetProfit >= 0 ? 'primary-stat' : '' }}" style="{{ $companyNetProfit < 0 ? 'background:#fff1f2!important;border-color:#fecdd3!important;' : '' }}">
            <small>Net Contribution</small>
            <div class="val" style="color: {{ $companyNetProfit >= 0 ? '#15803d' : '#b91c1c' }};">
              ৳ {{ number_format($companyNetProfit, 2) }}
              <span style="font-size: 11px; font-weight: 700;">({{ $companyNetMarginPct }}%)</span>
            </div>
          </div>
        </div>

        {{-- Section 1: Branch-Wise Revenue & Sales Performance --}}
        <div>
          <div class="doc-section-title">
            <span>1. Branch-Wise Revenue &amp; Sales Performance</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 30px;" class="text-center">Rank</th>
                  <th>Branch</th>
                  <th class="text-end" style="width: 110px;">Orders</th>
                  <th class="text-end" style="width: 110px;">Units Sold</th>
                  <th class="text-end" style="width: 125px;">Gross Sales</th>
                  <th class="text-end" style="width: 110px;">Discounts</th>
                  <th class="text-end" style="width: 110px;">Returns</th>
                  <th class="text-end" style="width: 130px;">Net Revenue</th>
                  <th class="text-end" style="width: 75px;">Rev. Share</th>
                </tr>
              </thead>
              <tbody>
                @forelse($branchPerformance as $idx => $bRow)
                  <tr>
                    <td class="text-center">
                      <span class="rank-badge rank-{{ $idx < 3 ? $idx + 1 : '' }}">{{ $idx + 1 }}</span>
                    </td>
                    <td>
                      <strong>{{ $bRow->branch->name }}</strong>
                      @if($bRow->branch->address)
                        <span style="display: block; font-size: 9px; color: #64748b;">{{ $bRow->branch->address }}</span>
                      @endif
                    </td>
                    <td class="text-end">{{ number_format($bRow->order_count) }}</td>
                    <td class="text-end">{{ number_format($bRow->units_sold) }}</td>
                    <td class="text-end">৳ {{ number_format($bRow->gross_net_sales + $bRow->total_discounts, 2) }}</td>
                    <td class="text-end" style="color: #b91c1c;">- ৳ {{ number_format($bRow->total_discounts, 2) }}</td>
                    <td class="text-end" style="color: #b91c1c;">- ৳ {{ number_format($bRow->return_amount, 2) }}</td>
                    <td class="text-end fw-bold" style="color: #1e3a8a;">৳ {{ number_format($bRow->net_realized_revenue, 2) }}</td>
                    <td class="text-end">{{ $bRow->revenue_share_pct }}%</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center text-muted py-2">No branch sales activity recorded in this period.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">ALL BRANCHES TOTAL:</td>
                  <td class="text-end">{{ number_format($companyOrders) }}</td>
                  <td class="text-end">{{ number_format($companyUnits) }}</td>
                  <td class="text-end">৳ {{ number_format($companyRevenue + $companyDiscounts, 2) }}</td>
                  <td class="text-end">- ৳ {{ number_format($companyDiscounts, 2) }}</td>
                  <td class="text-end">- ৳ {{ number_format($companyReturns, 2) }}</td>
                  <td class="text-end" style="color: #1e3a8a;">৳ {{ number_format($companyRevenue, 2) }}</td>
                  <td class="text-end">100.0%</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Section 2: Branch-Wise Profitability & OPEX Analysis --}}
        <div>
          <div class="doc-section-title">
            <span>2. Branch-Wise Profitability &amp; Operating Expense Analysis</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 30px;" class="text-center">#</th>
                  <th>Branch</th>
                  <th class="text-end" style="width: 125px;">Net Revenue</th>
                  <th class="text-end" style="width: 115px;">COGS</th>
                  <th class="text-end" style="width: 125px;">Gross Profit</th>
                  <th class="text-end" style="width: 65px;">GM%</th>
                  <th class="text-end" style="width: 110px;">Branch OPEX</th>
                  <th class="text-end" style="width: 130px;">Net Contribution</th>
                  <th class="text-end" style="width: 65px;">NM%</th>
                </tr>
              </thead>
              <tbody>
                @forelse($branchPerformance as $idx => $bRow)
                  <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $bRow->branch->name }}</strong></td>
                    <td class="text-end">৳ {{ number_format($bRow->net_realized_revenue, 2) }}</td>
                    <td class="text-end">৳ {{ number_format($bRow->cogs, 2) }}</td>
                    <td class="text-end fw-bold" style="color: #0369a1;">৳ {{ number_format($bRow->realized_gross_profit, 2) }}</td>
                    <td class="text-end" style="color: #0369a1;">{{ $bRow->gross_margin_pct }}%</td>
                    <td class="text-end" style="color: #b45309;">৳ {{ number_format($bRow->opex, 2) }}</td>
                    <td class="text-end fw-bold" style="color: {{ $bRow->net_contribution >= 0 ? '#15803d' : '#b91c1c' }};">
                      ৳ {{ number_format($bRow->net_contribution, 2) }}
                    </td>
                    <td class="text-end" style="color: {{ $bRow->net_margin_pct >= 0 ? '#15803d' : '#b91c1c' }};">
                      {{ $bRow->net_margin_pct }}%
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center text-muted py-2">No data available.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">CONSOLIDATED TOTAL:</td>
                  <td class="text-end">৳ {{ number_format($companyRevenue, 2) }}</td>
                  <td class="text-end">৳ {{ number_format($companyCogs, 2) }}</td>
                  <td class="text-end" style="color: #0369a1;">৳ {{ number_format($companyGrossProfit, 2) }}</td>
                  <td class="text-end" style="color: #0369a1;">{{ $companyGrossMarginPct }}%</td>
                  <td class="text-end" style="color: #b45309;">৳ {{ number_format($companyOpex, 2) }}</td>
                  <td class="text-end fw-bold" style="color: {{ $companyNetProfit >= 0 ? '#15803d' : '#b91c1c' }};">
                    ৳ {{ number_format($companyNetProfit, 2) }}
                  </td>
                  <td class="text-end" style="color: {{ $companyNetMarginPct >= 0 ? '#15803d' : '#b91c1c' }};">
                    {{ $companyNetMarginPct }}%
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Section 3: Branch-Wise Cash Collection & Receivables --}}
        <div>
          <div class="doc-section-title">
            <span>3. Branch-Wise Cash Collection &amp; Accounts Receivable</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 30px;" class="text-center">#</th>
                  <th>Branch</th>
                  <th class="text-end" style="width: 130px;">Net Revenue</th>
                  <th class="text-end" style="width: 130px;">Cash Collected</th>
                  <th class="text-end" style="width: 130px;">Customer Due (AR)</th>
                  <th class="text-end" style="width: 130px;">Branch Cash Vault</th>
                  <th class="text-end" style="width: 90px;">Collection%</th>
                </tr>
              </thead>
              <tbody>
                @forelse($branchPerformance as $idx => $bRow)
                  @php
                    $collectionPct = $bRow->gross_net_sales > 0
                      ? round(($bRow->cash_collected / $bRow->gross_net_sales) * 100, 1)
                      : 0;
                  @endphp
                  <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $bRow->branch->name }}</strong></td>
                    <td class="text-end">৳ {{ number_format($bRow->net_realized_revenue, 2) }}</td>
                    <td class="text-end fw-bold" style="color: #15803d;">৳ {{ number_format($bRow->cash_collected, 2) }}</td>
                    <td class="text-end" style="color: {{ $bRow->customer_due > 0 ? '#b91c1c' : '#64748b' }};">
                      ৳ {{ number_format($bRow->customer_due, 2) }}
                    </td>
                    <td class="text-end" style="color: #0369a1;">৳ {{ number_format($bRow->branch_cash, 2) }}</td>
                    <td class="text-end">
                      <span class="doc-badge {{ $collectionPct >= 90 ? 'doc-badge-profit' : ($collectionPct >= 60 ? 'doc-badge-warn' : 'doc-badge-loss') }}">
                        {{ $collectionPct }}%
                      </span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-2">No collection data available.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">ALL BRANCHES:</td>
                  <td class="text-end">৳ {{ number_format($companyRevenue, 2) }}</td>
                  <td class="text-end" style="color: #15803d;">৳ {{ number_format($branchPerformance->sum('cash_collected'), 2) }}</td>
                  <td class="text-end" style="color: #b91c1c;">৳ {{ number_format($companyCustomerDue, 2) }}</td>
                  <td class="text-end" style="color: #0369a1;">৳ {{ number_format($companyCash, 2) }}</td>
                  <td class="text-end">—</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Section 4: Branch-Wise Inventory & Stock Strength --}}
        <div>
          <div class="doc-section-title">
            <span>4. Branch-Wise Current Inventory &amp; Stock Strength</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 30px;" class="text-center">#</th>
                  <th>Branch</th>
                  <th class="text-end" style="width: 100px;">Active SKUs</th>
                  <th class="text-end" style="width: 110px;">Units On-Hand</th>
                  <th class="text-end" style="width: 135px;">Cost Valuation</th>
                  <th class="text-end" style="width: 135px;">Retail Valuation</th>
                  <th class="text-end" style="width: 125px;">Potential Margin</th>
                  <th class="text-end" style="width: 90px;">SRs</th>
                </tr>
              </thead>
              <tbody>
                @forelse($branchPerformance as $idx => $bRow)
                  @php
                    $potentialMargin = max(0, $bRow->stock_retail_value - $bRow->stock_cost_value);
                  @endphp
                  <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $bRow->branch->name }}</strong></td>
                    <td class="text-end">{{ number_format($bRow->sku_count) }}</td>
                    <td class="text-end">{{ number_format($bRow->stock_units) }}</td>
                    <td class="text-end fw-bold">৳ {{ number_format($bRow->stock_cost_value, 2) }}</td>
                    <td class="text-end" style="color: #0369a1;">৳ {{ number_format($bRow->stock_retail_value, 2) }}</td>
                    <td class="text-end" style="color: #15803d;">৳ {{ number_format($potentialMargin, 2) }}</td>
                    <td class="text-end">{{ $bRow->sr_count }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-2">No inventory data available.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">COMPANY TOTAL:</td>
                  <td class="text-end">{{ number_format($branchPerformance->sum('sku_count')) }}</td>
                  <td class="text-end">{{ number_format($branchPerformance->sum('stock_units')) }}</td>
                  <td class="text-end">৳ {{ number_format($companyStockCostVal, 2) }}</td>
                  <td class="text-end" style="color: #0369a1;">৳ {{ number_format($branchPerformance->sum('stock_retail_value'), 2) }}</td>
                  <td class="text-end" style="color: #15803d;">৳ {{ number_format(max(0, $branchPerformance->sum('stock_retail_value') - $companyStockCostVal), 2) }}</td>
                  <td class="text-end">{{ $branchPerformance->sum('sr_count') }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Section 5: Avg Order Value & Customer Activity --}}
        <div>
          <div class="doc-section-title">
            <span>5. Branch-Wise Average Order Value &amp; Customer Activity</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 30px;" class="text-center">#</th>
                  <th>Branch</th>
                  <th class="text-end" style="width: 110px;">Orders</th>
                  <th class="text-end" style="width: 130px;">Unique Customers</th>
                  <th class="text-end" style="width: 130px;">Avg Order Value</th>
                  <th class="text-end" style="width: 110px;">Returns</th>
                  <th class="text-end" style="width: 90px;">Return%</th>
                  <th class="text-end" style="width: 90px;">Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($branchPerformance as $idx => $bRow)
                  @php
                    $returnPct = $bRow->gross_net_sales > 0
                      ? round(($bRow->return_amount / ($bRow->gross_net_sales + $bRow->total_discounts + 0.01)) * 100, 1)
                      : 0;
                  @endphp
                  <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $bRow->branch->name }}</strong></td>
                    <td class="text-end">{{ number_format($bRow->order_count) }}</td>
                    <td class="text-end">{{ number_format($bRow->customer_count) }}</td>
                    <td class="text-end fw-bold">৳ {{ number_format($bRow->avg_order_value, 2) }}</td>
                    <td class="text-end" style="color: #b91c1c;">
                      ৳ {{ number_format($bRow->return_amount, 2) }}
                      @if($bRow->return_count > 0)
                        <span style="font-size: 9px; color: #64748b;">({{ $bRow->return_count }}x)</span>
                      @endif
                    </td>
                    <td class="text-end">{{ $returnPct }}%</td>
                    <td class="text-end">
                      @if($bRow->net_realized_revenue > 0)
                        <span class="doc-badge doc-badge-profit">Active</span>
                      @else
                        <span class="doc-badge doc-badge-info">Idle</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-2">No activity data available.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">COMBINED TOTALS:</td>
                  <td class="text-end">{{ number_format($companyOrders) }}</td>
                  <td class="text-end">{{ number_format($branchPerformance->sum('customer_count')) }}</td>
                  <td class="text-end">৳ {{ number_format($companyOrders > 0 ? $companyRevenue / $companyOrders : 0, 2) }}</td>
                  <td class="text-end" style="color: #b91c1c;">৳ {{ number_format($companyReturns, 2) }}</td>
                  <td class="text-end">—</td>
                  <td class="text-end">—</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Official Signatures Section --}}
        <div class="doc-signatures-row pdf-section">
          <div class="doc-sign-col">
            <div class="doc-sign-line"></div>
            <p>Prepared By</p>
            <small>{{ auth()->user()->fullname ?? 'System Admin' }}</small>
          </div>
          <div class="doc-sign-col">
            <div class="doc-sign-line"></div>
            <p>Head of Operations</p>
            <small>Branch Performance Audit</small>
          </div>
          <div class="doc-sign-col">
            <div class="doc-sign-line"></div>
            <p>Authorized Signature</p>
            <small>Managing Director / Executive Board</small>
          </div>
        </div>

        {{-- Audit & System Disclaimer Footer --}}
        <div class="doc-audit-footer pdf-section">
          <div>
            Generated via <strong>R-ELECTRIC DMS Reporting Engine</strong> (Automated &amp; Certified)
          </div>
          <div>
            Report Code: <span style="font-family: monospace;">BR-PERF-{{ $startDate->format('ymd') }}-{{ $endDate->format('ymd') }}</span> | Branch Comparison Statement
          </div>
        </div>

      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  /**
   * Client-side high-resolution A4 PDF Generation using html2pdf.js
   */
  function downloadA4PDF() {
    const element = document.getElementById('reportPrintArea');
    const btn = document.getElementById('btnDownloadPdf');

    const originalBtnHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating PDF...';
    btn.disabled = true;

    const opt = {
      margin:       [6, 6, 6, 6],
      filename:     'Branch_Performance_Report_{{ $startDate->format('Ymd') }}_to_{{ $endDate->format('Ymd') }}.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  {
        scale: 2,
        useCORS: true,
        logging: false,
        letterRendering: true,
        scrollY: 0
      },
      jsPDF:        {
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
      },
      pagebreak:    {
        mode: 'legacy',
        avoid: ['.doc-signatures-row', '.doc-audit-footer', '.doc-summary-grid', '.doc-meta-bar', '.doc-header-row', 'tfoot']
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

