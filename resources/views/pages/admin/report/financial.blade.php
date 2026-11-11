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
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

  /* Executive Financial Highlights */
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

  .doc-summary-box.danger-stat {
    background: #fff1f2 !important;
    border-color: #fecdd3 !important;
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

  /* Specific Financial Table Formattings */
  .pnl-category-header td {
    background: #f1f5f9 !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    font-size: 10px !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border-top: 1.5px solid #94a3b8 !important;
  }

  .pnl-subtotal-row td {
    background: #f8fafc !important;
    font-weight: 700 !important;
    border-top: 1px solid #cbd5e1 !important;
    border-bottom: 1px solid #cbd5e1 !important;
  }

  .pnl-major-row td {
    background: #e2e8f0 !important;
    font-weight: 800 !important;
    font-size: 11.5px !important;
    border-top: 2px solid #0f172a !important;
    border-bottom: 2px solid #0f172a !important;
  }

  /* Status Badges in Document */
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

  /* Avoid page breaks only for signature/footer — never force entire sections */
  .doc-signatures-row,
  .doc-audit-footer,
  .doc-summary-grid,
  .doc-meta-bar,
  .doc-header-row {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }

  /* pdf-section: only used for signatures & footer now */
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

    /* Hide ALL screen UI chrome — sidebar, navbar, overlays, filters */
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

    /* Expand main content to full page width with no left offset */
    body,
    html {
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

    /* Compact Mobile Table Styling */
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
      width: auto !important;
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

    .doc-table-compact .branch-sub-address {
      display: block;
      font-size: 7.5px !important;
      line-height: 1.1;
      margin-left: 0 !important;
      margin-top: 2px;
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
        <i class="fas fa-file-invoice-dollar text-primary"></i> Executive Financial Report
      </h2>
      <p class="text-muted mb-0 small">
        Comprehensive company-wide Profit &amp; Loss (Income Statement), cost of sales, operating overheads, and cash flow liquidity.
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
    <form method="GET" action="{{ route('admin.report.financial') }}" id="financialFilterForm">

      {{-- Filter Inputs Grid --}}
      <div class="filter-form-grid">
        {{-- Start Date --}}
        <div class="filter-field">
          <label><i class="fas fa-calendar-alt me-1 text-primary"></i> From Date</label>
          <input type="date" name="start_date" id="startDate" class="form-control" value="{{ $startDate->toDateString() }}">
        </div>

        {{-- End Date --}}
        <div class="filter-field">
          <label><i class="fas fa-calendar-alt me-1 text-primary"></i> To Date</label>
          <input type="date" name="end_date" id="endDate" class="form-control" value="{{ $endDate->toDateString() }}">
        </div>

        {{-- Branch --}}
        <div class="filter-field">
          <label><i class="fas fa-building me-1 text-primary"></i> Entity Scope</label>
          <select name="branch_id" class="form-select">
            <option value="all">-- Full Company (Consolidated) --</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                {{ $b->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Action Buttons --}}
        <div class="filter-field d-flex gap-2">
          <button type="submit" class="btn-smart btn-blue w-100">
            <i class="fas fa-sliders me-1"></i> Generate
          </button>
          <a href="{{ route('admin.report.financial') }}" class="btn-smart btn-gray" title="Reset Filters">
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
        <small>Net Realized Revenue</small>
        <h4>৳ {{ number_format($netRealizedRevenue, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-purple-soft">
        <i class="fas fa-boxes-packing"></i>
      </div>
      <div class="kpi-info">
        <small>Cost of Goods Sold (COGS)</small>
        <h4>৳ {{ number_format($netRealizedCogs, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-emerald-soft">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="kpi-info">
        <small>Gross Trading Profit</small>
        <h4 style="color: #059669;">৳ {{ number_format($realizedGrossProfit, 2) }} <span class="fs-6 text-muted fw-normal">({{ $grossMarginPercent }}%)</span></h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-amber-soft">
        <i class="fas fa-money-bill-wave"></i>
      </div>
      <div class="kpi-info">
        <small>Operating Expenses (OPEX)</small>
        <h4 class="text-warning-emphasis">৳ {{ number_format($totalOpex, 2) }}</h4>
      </div>
    </div>

    @if(!$branchId && $totalBonuses > 0)
    <div class="kpi-card">
      <div class="kpi-icon bg-cyan-soft">
        <i class="fas fa-gift"></i>
      </div>
      <div class="kpi-info">
        <small>Company Bonus / Incentives</small>
        <h4 style="color: #0891b2;">+ ৳ {{ number_format($totalBonuses, 2) }}</h4>
      </div>
    </div>
    @endif

    <div class="kpi-card">
      <div class="kpi-icon {{ $netProfit >= 0 ? 'bg-green-soft' : 'bg-rose-soft' }}">
        <i class="fas fa-scale-balanced"></i>
      </div>
      <div class="kpi-info">
        <small>Net Comprehensive Profit</small>
        <h4 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
          ৳ {{ number_format($netProfit, 2) }}
          <span class="fs-6 fw-normal">({{ $netMarginPercent }}%)</span>
        </h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-blue-soft">
        <i class="fas fa-vault"></i>
      </div>
      <div class="kpi-info">
        <small>Total Company Cash</small>
        <h4>৳ {{ number_format($totalCompanyCash, 2) }}</h4>
      </div>
    </div>
  </div>

  {{-- The A4 Printable / Exportable Document --}}
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
            <h1>EXECUTIVE FINANCIAL STATEMENT</h1>
            <div class="doc-badge-pill">Consolidated Profit &amp; Loss (Income Statement)</div>
          </div>
        </div>

        {{-- Document Metadata Bar --}}
        <div class="doc-meta-bar">
          <div class="doc-meta-item">
            <strong>Statement Period</strong>
            <span>{{ $startDate->format('d M, Y') }} — {{ $endDate->format('d M, Y') }}</span>
          </div>
          <div class="doc-meta-item">
            <strong>Entity Scope</strong>
            <span>
              @if(request('branch_id') && request('branch_id') !== 'all')
                {{ optional($branches->firstWhere('id', request('branch_id')))->name ?? 'Selected Branch' }}
              @else
                Full Company (Consolidated)
              @endif
            </span>
          </div>
          <div class="doc-meta-item">
            <strong>Accounting Basis</strong>
            <span>Accrual &amp; Realized Cash Basis</span>
          </div>
          <div class="doc-meta-item" style="text-align: right;">
            <strong>Generated On &amp; By</strong>
            <span>{{ now()->format('d M, Y h:i A') }} ({{ auth()->user()->fullname ?? 'Admin' }})</span>
          </div>
        </div>

        {{-- Executive Financial Highlights Matrix --}}
        <div class="doc-summary-grid">
          <div class="doc-summary-box">
            <small>Net Realized Turnover</small>
            <div class="val">৳ {{ number_format($netRealizedRevenue, 2) }}</div>
          </div>
          <div class="doc-summary-box">
            <small>Gross Trading Margin</small>
            <div class="val" style="color: #0369a1;">
              ৳ {{ number_format($realizedGrossProfit, 2) }}
              <span style="font-size: 11px; font-weight: 600;">({{ $grossMarginPercent }}%)</span>
            </div>
          </div>
          <div class="doc-summary-box">
            <small>Total Operating Overheads</small>
            <div class="val" style="color: #b45309;">৳ {{ number_format($totalOpex, 2) }}</div>
          </div>
          <div class="doc-summary-box {{ $netProfit >= 0 ? 'primary-stat' : 'danger-stat' }}">
            <small>Net Realized {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</small>
            <div class="val" style="color: {{ $netProfit >= 0 ? '#15803d' : '#b91c1c' }};">
              ৳ {{ number_format($netProfit, 2) }}
              <span style="font-size: 11px; font-weight: 700;">({{ $netMarginPercent }}%)</span>
            </div>
            @if(!$branchId && $totalBonuses > 0)
              <div style="font-size: 8px; color: #15803d; font-weight: 600; margin-top: 1px;">(Incl. +৳{{ number_format($totalBonuses, 0) }} Bonus)</div>
            @endif
          </div>
        </div>

        {{-- Section 1: Comprehensive Profit & Loss Statement (Trading & Income Statement) --}}
        <div>
          <div class="doc-section-title">
            <span>1. Formal Statement of Profit &amp; Loss (Income Statement)</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th>Particulars / Financial Line Items</th>
                  <th class="text-end" style="width: 140px;">Sub-Total (৳)</th>
                  <th class="text-end" style="width: 160px;">Net Amount (৳)</th>
                  <th class="text-end" style="width: 80px;">% Share</th>
                </tr>
              </thead>
              <tbody>
                {{-- Part I: Turnover / Revenue --}}
                <tr class="pnl-category-header">
                  <td colspan="4">I. Revenue &amp; Trading Turnover</td>
                </tr>
                <tr>
                  <td style="padding-left: 20px !important;">Gross Invoiced Sales Volume</td>
                  <td class="text-end">৳ {{ number_format($totalGrossSales, 2) }}</td>
                  <td class="text-end"></td>
                  <td class="text-end"></td>
                </tr>
                <tr>
                  <td style="padding-left: 20px !important; color: #b91c1c;">Less: Commercial, Offer &amp; Special Discounts</td>
                  <td class="text-end" style="color: #b91c1c;">- ৳ {{ number_format($totalDiscounts, 2) }}</td>
                  <td class="text-end"></td>
                  <td class="text-end"></td>
                </tr>
                <tr class="pnl-subtotal-row">
                  <td style="padding-left: 20px !important; font-weight: 700;">Net Invoiced Sales Revenue</td>
                  <td class="text-end"></td>
                  <td class="text-end fw-bold">৳ {{ number_format($totalNetSales, 2) }}</td>
                  <td class="text-end">{{ $netRealizedRevenue > 0 ? round(($totalNetSales / $netRealizedRevenue) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                  <td style="padding-left: 20px !important; color: #b91c1c;">Less: Sales Returns &amp; Customer Allowances ({{ $totalReturnsCount }} claims)</td>
                  <td class="text-end"></td>
                  <td class="text-end" style="color: #b91c1c;">- ৳ {{ number_format($totalReturnsAmount, 2) }}</td>
                  <td class="text-end" style="color: #b91c1c;">{{ $netRealizedRevenue > 0 ? round(($totalReturnsAmount / $netRealizedRevenue) * 100, 1) : 0 }}%</td>
                </tr>
                <tr class="pnl-major-row" style="background: #eff6ff !important;">
                  <td style="color: #1e3a8a;"><strong>A. NET REALIZED TRADING TURNOVER (REVENUE)</strong></td>
                  <td class="text-end"></td>
                  <td class="text-end fw-bold" style="color: #1e3a8a; font-size: 12px;">৳ {{ number_format($netRealizedRevenue, 2) }}</td>
                  <td class="text-end fw-bold" style="color: #1e3a8a;">100.0%</td>
                </tr>

                {{-- Part II: Cost of Goods Sold --}}
                <tr class="pnl-category-header">
                  <td colspan="4">II. Cost of Goods Sold (COGS)</td>
                </tr>
                <tr>
                  <td style="padding-left: 20px !important;">Gross Procurement Cost of Merchandise Sold</td>
                  <td class="text-end">৳ {{ number_format($grossCogs, 2) }}</td>
                  <td class="text-end"></td>
                  <td class="text-end"></td>
                </tr>
                <tr>
                  <td style="padding-left: 20px !important; color: #15803d;">Less: Cost of Returned Inventory Restored to Stock</td>
                  <td class="text-end" style="color: #15803d;">- ৳ {{ number_format($returnedCogs, 2) }}</td>
                  <td class="text-end"></td>
                  <td class="text-end"></td>
                </tr>
                <tr class="pnl-subtotal-row" style="color: #475569;">
                  <td style="padding-left: 20px !important; font-weight: 700;"><strong>B. NET REALIZED COST OF SALES (COGS)</strong></td>
                  <td class="text-end"></td>
                  <td class="text-end fw-bold" style="color: #475569;">৳ {{ number_format($netRealizedCogs, 2) }}</td>
                  <td class="text-end fw-semibold">{{ $netRealizedRevenue > 0 ? round(($netRealizedCogs / $netRealizedRevenue) * 100, 1) : 0 }}%</td>
                </tr>

                {{-- Part III: Gross Profit --}}
                <tr class="pnl-major-row" style="background: #f0fdf4 !important;">
                  <td style="color: #14532d;"><strong>III. GROSS TRADING PROFIT (A - B)</strong></td>
                  <td class="text-end"></td>
                  <td class="text-end fw-bold" style="color: #15803d; font-size: 12px;">৳ {{ number_format($realizedGrossProfit, 2) }}</td>
                  <td class="text-end fw-bold" style="color: #15803d;">{{ $grossMarginPercent }}%</td>
                </tr>

                {{-- Part IV: Operating Expenses --}}
                <tr class="pnl-category-header">
                  <td colspan="4">IV. Operating Expenses &amp; Overheads (OPEX)</td>
                </tr>
                <tr>
                  <td style="padding-left: 20px !important;">Branch-Level Direct Operating Costs</td>
                  <td class="text-end">৳ {{ number_format($totalBranchCosts, 2) }}</td>
                  <td class="text-end"></td>
                  <td class="text-end"></td>
                </tr>
                @if(!$branchId)
                <tr>
                  <td style="padding-left: 20px !important;">Central Corporate &amp; Headquarters Overheads</td>
                  <td class="text-end">৳ {{ number_format($totalCompanyCosts, 2) }}</td>
                  <td class="text-end"></td>
                  <td class="text-end"></td>
                </tr>
                @endif
                <tr class="pnl-subtotal-row" style="color: #b45309;">
                  <td style="padding-left: 20px !important; font-weight: 700;"><strong>C. TOTAL OPERATING OVERHEADS (OPEX)</strong></td>
                  <td class="text-end"></td>
                  <td class="text-end fw-bold" style="color: #b45309;">৳ {{ number_format($totalOpex, 2) }}</td>
                  <td class="text-end fw-semibold">{{ $netRealizedRevenue > 0 ? round(($totalOpex / $netRealizedRevenue) * 100, 1) : 0 }}%</td>
                </tr>

                {{-- Part V: Operating Trading Profit --}}
                <tr class="pnl-subtotal-row" style="background: #f8fafc !important;">
                  <td style="padding-left: 20px !important; font-weight: 700; color: #1e293b;">
                    <strong>V. NET OPERATING TRADING PROFIT (III - C)</strong>
                  </td>
                  <td class="text-end"></td>
                  <td class="text-end fw-bold" style="color: {{ $operatingProfit >= 0 ? '#15803d' : '#b91c1c' }};">
                    ৳ {{ number_format($operatingProfit, 2) }}
                  </td>
                  <td class="text-end fw-semibold">{{ $netRealizedRevenue > 0 ? round(($operatingProfit / $netRealizedRevenue) * 100, 1) : 0 }}%</td>
                </tr>

                {{-- Part VI: Company Bonus & Extra Income --}}
                @if(!$branchId && $totalBonuses > 0)
                <tr class="pnl-category-header" style="background: #f0fdf4 !important; border-top: 1.5px solid #86efac !important;">
                  <td colspan="4" style="color: #166534 !important;">VI. Company Extra Income &amp; Financial Incentives</td>
                </tr>
                <tr>
                  <td style="padding-left: 20px !important; color: #15803d;">
                    Company Bonus &amp; Financial Gifts Received ({{ $bonusesCount }} entries)
                  </td>
                  <td class="text-end" style="color: #15803d;">+ ৳ {{ number_format($totalBonuses, 2) }}</td>
                  <td class="text-end fw-bold" style="color: #15803d;">+ ৳ {{ number_format($totalBonuses, 2) }}</td>
                  <td class="text-end fw-semibold" style="color: #15803d;">{{ $netRealizedRevenue > 0 ? round(($totalBonuses / $netRealizedRevenue) * 100, 1) : 0 }}%</td>
                </tr>
                @endif

                {{-- Final / Net Comprehensive Profit --}}
                <tr class="pnl-major-row" style="background: {{ $netProfit >= 0 ? '#dcfce7' : '#fee2e2' }} !important; border-top: 2.5px solid #0f172a !important; border-bottom: 2.5px solid #0f172a !important;">
                  <td style="color: {{ $netProfit >= 0 ? '#14532d' : '#7f1d1d' }};">
                    <strong>{{ (!$branchId && $totalBonuses > 0) ? 'VII' : 'VI' }}. NET COMPREHENSIVE {{ $netProfit >= 0 ? 'PROFIT' : 'LOSS' }} {{ (!$branchId && $totalBonuses > 0) ? '(V + VI)' : '(III - C)' }}</strong>
                  </td>
                  <td class="text-end"></td>
                  <td class="text-end fw-bold" style="color: {{ $netProfit >= 0 ? '#15803d' : '#b91c1c' }}; font-size: 13px;">
                    ৳ {{ number_format($netProfit, 2) }}
                  </td>
                  <td class="text-end fw-bold" style="color: {{ $netProfit >= 0 ? '#15803d' : '#b91c1c' }};">
                    {{ $netMarginPercent }}%
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        {{-- Section 2: Operating Expense Breakdown by Category --}}
        <div>
          <div class="doc-section-title">
            <span>2. Operating Expense Breakdown by Category</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 25px;" class="text-center">#</th>
                  <th>Expense Category</th>
                  <th class="text-end" style="width: 140px;">Branch Overheads</th>
                  <th class="text-end" style="width: 140px;">Corporate / HQ</th>
                  <th class="text-end" style="width: 150px;">Total Outlay (৳)</th>
                  <th class="text-end" style="width: 85px;">% of OPEX</th>
                </tr>
              </thead>
              <tbody>
                @forelse($expenseCategories as $eIdx => $eRow)
                  <tr>
                    <td class="text-center">{{ $eIdx + 1 }}</td>
                    <td><strong>{{ $eRow->name }}</strong></td>
                    <td class="text-end">৳ {{ number_format($eRow->branch_cost, 2) }}</td>
                    <td class="text-end">৳ {{ number_format($eRow->company_cost, 2) }}</td>
                    <td class="text-end fw-bold">৳ {{ number_format($eRow->total_cost, 2) }}</td>
                    <td class="text-end">{{ $eRow->share_percent }}%</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-2">No operating expenses recorded in this period.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">CONSOLIDATED OPEX TOTAL:</td>
                  <td class="text-end">৳ {{ number_format($totalBranchCosts, 2) }}</td>
                  <td class="text-end">৳ {{ number_format($totalCompanyCosts, 2) }}</td>
                  <td class="text-end" style="color: #b45309;">৳ {{ number_format($totalOpex, 2) }}</td>
                  <td class="text-end">100.0%</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Section 3: Multi-Branch Profitability & Financial Contribution --}}
        <div>
          <div class="doc-section-title">
            <span>3. Multi-Branch Profitability Contribution</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 25px;" class="text-center">#</th>
                  <th>Operating Branch</th>
                  <th class="text-end" style="width: 125px;">Net Revenue</th>
                  <th class="text-end" style="width: 115px;">Cost of Sales</th>
                  <th class="text-end" style="width: 115px;">Gross Margin</th>
                  <th class="text-end" style="width: 110px;">Branch OPEX</th>
                  <th class="text-end" style="width: 130px;">Net Contribution</th>
                  <th class="text-end" style="width: 70px;">Share</th>
                </tr>
              </thead>
              <tbody>
                @forelse($branchContributions as $bIdx => $bRow)
                  <tr>
                    <td class="text-center">{{ $bIdx + 1 }}</td>
                    <td>
                      <strong>{{ $bRow->branch->name ?? 'Head Office / Depot' }}</strong>
                      @if($bRow->branch?->address)
                        <span class="branch-sub-address" style="font-size: 9px; color: #64748b; margin-left: 4px;">({{ $bRow->branch->address }})</span>
                      @endif
                    </td>
                    <td class="text-end">৳ {{ number_format($bRow->realized_revenue, 2) }}</td>
                    <td class="text-end">৳ {{ number_format($bRow->cogs, 2) }}</td>
                    <td class="text-end fw-bold" style="color: #0369a1;">৳ {{ number_format($bRow->gross_profit, 2) }}</td>
                    <td class="text-end" style="color: #b45309;">৳ {{ number_format($bRow->opex, 2) }}</td>
                    <td class="text-end fw-bold" style="color: {{ $bRow->net_contribution >= 0 ? '#15803d' : '#b91c1c' }};">
                      ৳ {{ number_format($bRow->net_contribution, 2) }}
                    </td>
                    <td class="text-end">{{ $bRow->share_percent }}%</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-2">No branch trading activity recorded.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">ALL BRANCHES COMBINED:</td>
                  <td class="text-end">৳ {{ number_format($netRealizedRevenue, 2) }}</td>
                  <td class="text-end">৳ {{ number_format($netRealizedCogs, 2) }}</td>
                  <td class="text-end" style="color: #0369a1;">৳ {{ number_format($realizedGrossProfit, 2) }}</td>
                  <td class="text-end" style="color: #b45309;">৳ {{ number_format($totalBranchCosts, 2) }}</td>
                  <td class="text-end fw-bold" style="color: {{ ($realizedGrossProfit - $totalBranchCosts) >= 0 ? '#15803d' : '#b91c1c' }};">
                    ৳ {{ number_format($realizedGrossProfit - $totalBranchCosts, 2) }}
                  </td>
                  <td class="text-end">100.0%</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Section 4: Treasury Cash Flow & Liquidity Register --}}
        <div>
          <div class="doc-section-title">
            <span>4. Treasury Cash Flow &amp; Liquidity Standing</span>
          </div>
          <table class="doc-table" style="margin-bottom: 15px;">
            <thead>
              <tr>
                <th>Treasury Flow / Balance Sheet Line Item</th>
                <th class="text-center" style="width: 110px;">Classification</th>
                <th class="text-end" style="width: 150px;">Financial Value (৳)</th>
                <th style="width: 140px;">Status / Remarks</th>
              </tr>
            </thead>
            <tbody>
              {{-- Inflow --}}
              <tr>
                <td><strong>Customer Collections (Sales Cash Realized)</strong></td>
                <td class="text-center"><span class="doc-badge doc-badge-profit">Cash Inflow</span></td>
                <td class="text-end fw-bold" style="color: #15803d;">৳ {{ number_format($totalCashCollected, 2) }}</td>
                <td style="color: #15803d; font-size: 9.5px;">Cleared in Treasury</td>
              </tr>
              @if(!$branchId && $totalBonuses > 0)
              <tr>
                <td><strong>Company Bonus &amp; Financial Incentives Realized</strong></td>
                <td class="text-center"><span class="doc-badge doc-badge-profit">Extra Inflow</span></td>
                <td class="text-end fw-bold" style="color: #15803d;">+ ৳ {{ number_format($totalBonuses, 2) }}</td>
                <td style="color: #15803d; font-size: 9.5px;">Supplier / Partner Bonus</td>
              </tr>
              @endif
              {{-- Receivables --}}
              <tr>
                <td>Accounts Receivable (Outstanding Customer Due)</td>
                <td class="text-center"><span class="doc-badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">Asset / Due</span></td>
                <td class="text-end fw-bold" style="color: #0369a1;">৳ {{ number_format($totalCustomerDue, 2) }}</td>
                <td style="color: #0369a1; font-size: 9.5px;">Pending Realization</td>
              </tr>
              {{-- Supplier Payments --}}
              <tr>
                <td>Procurement Payments Disbursed to Suppliers</td>
                <td class="text-center"><span class="doc-badge doc-badge-loss">Cash Outflow</span></td>
                <td class="text-end fw-bold" style="color: #b91c1c;">- ৳ {{ number_format($totalSupplierPaid, 2) }}</td>
                <td style="color: #b91c1c; font-size: 9.5px;">Settled to Suppliers</td>
              </tr>
              {{-- Operating Overheads --}}
              <tr>
                <td>Operating Overhead Cash Outlay (OPEX)</td>
                <td class="text-center"><span class="doc-badge doc-badge-loss">Cash Outflow</span></td>
                <td class="text-end fw-bold" style="color: #b45309;">- ৳ {{ number_format($totalOpex, 2) }}</td>
                <td style="color: #b45309; font-size: 9.5px;">Operational Expense</td>
              </tr>
              {{-- Payables --}}
              <tr>
                <td>Accounts Payable (Outstanding Supplier Due)</td>
                <td class="text-center"><span class="doc-badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;">Liability</span></td>
                <td class="text-end fw-bold" style="color: #991b1b;">৳ {{ number_format($totalSupplierDue, 2) }}</td>
                <td style="color: #991b1b; font-size: 9.5px;">Supplier Obligation</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td>TOTAL CURRENT LIQUID CASH RESERVES:</td>
                <td class="text-center"><span class="doc-badge doc-badge-profit">Vault Cash</span></td>
                <td class="text-end" style="color: #059669; font-size: 12px;">৳ {{ number_format($totalCompanyCash, 2) }}</td>
                <td style="color: #059669; font-size: 9.5px;">Admin: ৳ {{ number_format($totalAdminCash, 2) }} | Branches: ৳ {{ number_format($totalBranchCash, 2) }}</td>
              </tr>
            </tfoot>
          </table>
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
            <p>Head of Finance &amp; Audit</p>
            <small>Internal Financial Audit</small>
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
            Report Code: <span style="font-family: monospace;">SR-FIN-{{ $startDate->format('ymd') }}-{{ $endDate->format('ymd') }}</span> | Consolidated Statement
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
      margin:       [6, 6, 6, 6], // [top, left, bottom, right] in mm
      filename:     'Executive_Financial_Report_{{ $startDate->format('Ymd') }}_to_{{ $endDate->format('Ymd') }}.pdf',
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
        avoid: ['.doc-signatures-row', '.doc-audit-footer', '.doc-summary-grid', '.doc-meta-bar', '.doc-header-row', '.pnl-major-row', 'tfoot']
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

