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

  /* Status Badges in Document */
  .doc-badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
  }
  .doc-badge-approved { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .doc-badge-pending  { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
  .doc-badge-rejected { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

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

  /* Avoid page breaks inside logical units */
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

    /* Section 1 & Section 2 Compact Mobile Table Styling */
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
        <i class="fas fa-truck-ramp-box text-primary"></i> Executive Stock-In Report
      </h2>
      <p class="text-muted mb-0 small">
        Strategic procurement records, supplier invoice volume, inventory additions, and procurement financial statement.
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
    <form method="GET" action="{{ route('admin.report.stock-in') }}" id="stockInFilterForm">

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
          <label><i class="fas fa-building me-1 text-primary"></i> Branch</label>
          <select name="branch_id" class="form-select">
            <option value="all">-- All Branches --</option>
            @foreach($branches as $b)
              <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                {{ $b->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Supplier --}}
        <div class="filter-field">
          <label><i class="fas fa-industry me-1 text-primary"></i> Supplier</label>
          <select name="supplier_id" class="form-select">
            <option value="all">-- All Suppliers --</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                {{ $s->company_name ?? $s->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Status --}}
        <div class="filter-field">
          <label><i class="fas fa-filter me-1 text-primary"></i> Procurement Status</label>
          <select name="status" class="form-select">
            <option value="valid" {{ $status === 'valid' ? 'selected' : '' }}>Valid (Excl. Rejected)</option>
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved (In Stock)</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Review</option>
            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>

        {{-- Action Buttons --}}
        <div class="filter-field d-flex gap-2">
          <button type="submit" class="btn-smart btn-blue w-100">
            <i class="fas fa-sliders me-1"></i> Filter
          </button>
          <a href="{{ route('admin.report.stock-in') }}" class="btn-smart btn-gray" title="Reset Filters">
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
        <small>Gross Procurement</small>
        <h4>৳ {{ number_format($totalProcurementValue, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-emerald-soft">
        <i class="fas fa-shield-halved"></i>
      </div>
      <div class="kpi-info">
        <small>Realized (Net - Return)</small>
        <h4 style="color: #059669;">৳ {{ number_format($netRealizedProcurement, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-green-soft">
        <i class="fas fa-hand-holding-dollar"></i>
      </div>
      <div class="kpi-info">
        <small>Supplier Payments</small>
        <h4 class="text-success">৳ {{ number_format($totalSupplierPaid, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-rose-soft">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>
      <div class="kpi-info">
        <small>Outstanding Supplier Due</small>
        <h4 class="text-danger">৳ {{ number_format($totalSupplierDue, 2) }}</h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-purple-soft">
        <i class="fas fa-receipt"></i>
      </div>
      <div class="kpi-info">
        <small>Total Invoices</small>
        <h4>{{ number_format($totalRequestsCount) }} <span class="fs-6 text-muted fw-normal">Invoices</span></h4>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon bg-amber-soft">
        <i class="fas fa-boxes-stacked"></i>
      </div>
      <div class="kpi-info">
        <small>Units Procured</small>
        <h4>{{ number_format($totalUnitsProcured) }} <span class="fs-6 text-muted fw-normal">Units</span></h4>
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
        <div class="doc-header-row pdf-section">
          <div class="doc-brand">
            <img src="{{ asset('image/relectric-logo.png') }}" alt="R-ELECTRIC" class="img-fluid">
            <div class="company-sub">Dealership &amp; Inventory Management System</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
              Multi-Brand Authorized Electric &amp; Electronics Distribution
            </div>
          </div>
          <div class="doc-title-box">
            <h1>EXECUTIVE STOCK-IN REPORT</h1>
            <div class="doc-badge-pill">Consolidated Procurement Statement</div>
          </div>
        </div>

        {{-- Document Metadata Bar --}}
        <div class="doc-meta-bar pdf-section">
          <div class="doc-meta-item">
            <strong>Reporting Window</strong>
            <span>{{ $startDate->format('d M, Y') }} — {{ $endDate->format('d M, Y') }}</span>
          </div>
          <div class="doc-meta-item">
            <strong>Branch Scope</strong>
            <span>
              @if(request('branch_id') && request('branch_id') !== 'all')
                {{ optional($branches->firstWhere('id', request('branch_id')))->name ?? 'Selected Branch' }}
              @else
                All Company Branches
              @endif
            </span>
          </div>
          <div class="doc-meta-item">
            <strong>Supplier Scope</strong>
            <span>
              @if(request('supplier_id') && request('supplier_id') !== 'all')
                {{ optional($suppliers->firstWhere('id', request('supplier_id')))->company_name ?? 'Selected Supplier' }}
              @else
                All Registered Suppliers
              @endif
            </span>
          </div>
          <div class="doc-meta-item" style="text-align: right;">
            <strong>Generated On &amp; By</strong>
            <span>{{ now()->format('d M, Y h:i A') }} ({{ auth()->user()->fullname ?? 'Admin' }})</span>
          </div>
        </div>

        {{-- Executive Financial Highlights Matrix --}}
        <div class="doc-summary-grid pdf-section">
          <div class="doc-summary-box">
            <small>Gross Procurement Cost</small>
            <div class="val">৳ {{ number_format($totalProcurementValue, 2) }}</div>
          </div>
          <div class="doc-summary-box primary-stat">
            <small>Net Realized Purchase</small>
            <div class="val" style="color: #15803d;">৳ {{ number_format($netRealizedProcurement, 2) }}</div>
          </div>
          <div class="doc-summary-box">
            <small>Supplier Payments Settled</small>
            <div class="val" style="color: #0369a1;">৳ {{ number_format($totalSupplierPaid, 2) }}</div>
          </div>
          <div class="doc-summary-box danger-stat">
            <small>Current Supplier Due</small>
            <div class="val" style="color: #b91c1c;">৳ {{ number_format($totalSupplierDue, 2) }}</div>
          </div>
        </div>

        {{-- Section 1: Supplier Procurement & Volume Breakdown --}}
        <div class="pdf-section">
          <div class="doc-section-title">
            <span>1. Supplier Procurement &amp; Volume Breakdown</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 25px;" class="text-center">#</th>
                  <th>Supplier / Company</th>
                  <th class="text-center" style="width: 75px;">Invoices</th>
                  <th class="text-center" style="width: 90px;">Units Procured</th>
                  <th class="text-end" style="width: 125px;">Procurement (৳)</th>
                  <th class="text-end" style="width: 125px;">Current Due (৳)</th>
                  <th class="text-end" style="width: 70px;">% Share</th>
                </tr>
              </thead>
              <tbody>
                @forelse($supplierBreakdown as $sIdx => $sRow)
                  <tr>
                    <td class="text-center">{{ $sIdx + 1 }}</td>
                    <td>
                      <strong>{{ $sRow->supplier->company_name ?? $sRow->supplier->name ?? 'Supplier #' . $sRow->supplier_id }}</strong>
                      @if($sRow->supplier?->phone)
                        <span style="font-size: 9px; color: #64748b; margin-left: 4px;">({{ $sRow->supplier->phone }})</span>
                      @endif
                    </td>
                    <td class="text-center">{{ number_format($sRow->request_count) }}</td>
                    <td class="text-center">{{ number_format($sRow->units_procured) }} Units</td>
                    <td class="text-end fw-bold">৳ {{ number_format($sRow->total_purchase, 2) }}</td>
                    <td class="text-end" style="{{ $sRow->supplier_due > 0 ? 'color: #b91c1c; font-weight: 700;' : 'color: #15803d;' }}">
                      ৳ {{ number_format($sRow->supplier_due, 2) }}
                    </td>
                    <td class="text-end">{{ $sRow->share_percent }}%</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-2">No supplier procurement recorded in this period.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">CONSOLIDATED SUPPLIERS TOTAL:</td>
                  <td class="text-center">{{ number_format($totalRequestsCount) }}</td>
                  <td class="text-center">{{ number_format($totalUnitsProcured) }} Units</td>
                  <td class="text-end">৳ {{ number_format($totalProcurementValue, 2) }}</td>
                  <td class="text-end" style="color: #b91c1c;">৳ {{ number_format($totalSupplierDue, 2) }}</td>
                  <td class="text-end">100.0%</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Section 2: Branch-Wise Stock-In & Inventory Additions --}}
        <div class="pdf-section">
          <div class="doc-section-title">
            <span>2. Branch-Wise Stock-In &amp; Inventory Additions</span>
          </div>
          <div class="doc-table-responsive">
            <table class="doc-table doc-table-compact">
              <thead>
                <tr>
                  <th style="width: 25px;" class="text-center">#</th>
                  <th>Receiving Branch</th>
                  <th class="text-center" style="width: 75px;">Invoices</th>
                  <th class="text-center" style="width: 90px;">Units Received</th>
                  <th class="text-end" style="width: 140px;">Procurement Value (৳)</th>
                  <th class="text-end" style="width: 85px;">Allocation Share</th>
                </tr>
              </thead>
              <tbody>
                @forelse($branchBreakdown as $bIdx => $bRow)
                  <tr>
                    <td class="text-center">{{ $bIdx + 1 }}</td>
                    <td>
                      <strong>{{ $bRow->branch->name ?? 'Head Office / Central Depot' }}</strong>
                      @if($bRow->branch?->address)
                        <span class="branch-sub-address" style="font-size: 9px; color: #64748b; margin-left: 4px;">({{ $bRow->branch->address }})</span>
                      @endif
                    </td>
                    <td class="text-center">{{ number_format($bRow->request_count) }}</td>
                    <td class="text-center">{{ number_format($bRow->units_received) }} Units</td>
                    <td class="text-end fw-bold">৳ {{ number_format($bRow->branch_purchase, 2) }}</td>
                    <td class="text-end">{{ $bRow->share_percent }}%</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-2">No branch stock-in recorded in this period.</td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" class="text-end">ALL BRANCHES TOTAL:</td>
                  <td class="text-center">{{ number_format($totalRequestsCount) }}</td>
                  <td class="text-center">{{ number_format($totalUnitsProcured) }} Units</td>
                  <td class="text-end">৳ {{ number_format($totalProcurementValue, 2) }}</td>
                  <td class="text-end">100.0%</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        {{-- Section 3: Top Procured Products (Top 3) --}}
        <div class="pdf-section">
          <div class="doc-section-title">
            <span>3. Top Procured Products (Top 3 By Spend)</span>
          </div>
          <table class="doc-table">
            <thead>
              <tr>
                <th style="width: 35px;" class="text-center">#</th>
                <th>Product Description</th>
                <th class="text-center" style="width: 110px;">Units Procured</th>
                <th class="text-end" style="width: 150px;">Total Procurement Cost</th>
                <th class="text-end" style="width: 110px;">% Spend Share</th>
              </tr>
            </thead>
            <tbody>
              @forelse($topProducts as $pIdx => $pRow)
                <tr>
                  <td class="text-center">{{ $pIdx + 1 }}</td>
                  <td>
                    <strong>{{ $pRow->product->name ?? 'Product #' . $pRow->product_id }}</strong>
                    @if($pRow->product?->sku)
                      <span style="font-size: 9px; color: #64748b; margin-left: 5px;">(SKU: {{ $pRow->product->sku }})</span>
                    @endif
                  </td>
                  <td class="text-center fw-semibold">{{ number_format($pRow->total_quantity) }} Units</td>
                  <td class="text-end fw-bold">৳ {{ number_format($pRow->total_cost, 2) }}</td>
                  <td class="text-end fw-semibold">{{ $pRow->share_percent }}%</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-2">No product procurement recorded in this period.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Section 4: Procurement Health & Returns Adjustment Analysis --}}
        <div class="pdf-section">
          <div class="doc-section-title">
            <span>4. Procurement Health &amp; Returns Adjustment Analysis</span>
          </div>
          <table class="doc-table" style="margin-bottom: 15px;">
            <thead>
              <tr>
                <th>Procurement Health Category</th>
                <th class="text-center" style="width: 85px;">Count / Invoices</th>
                <th class="text-end" style="width: 125px;">Financial Value</th>
                <th class="text-end" style="width: 85px;">% of Total</th>
                <th style="width: 140px;">Status / Remarks</th>
              </tr>
            </thead>
            <tbody>
              {{-- Approved Stock-In --}}
              @php
                $approvedRow = $statusBreakdown['approved'] ?? null;
                $pendingRow  = $statusBreakdown['pending'] ?? null;
                $rejectedRow = $statusBreakdown['rejected'] ?? null;
              @endphp
              <tr>
                <td><span class="doc-badge doc-badge-approved">Approved</span> Stock-in received &amp; added to branch inventory</td>
                <td class="text-center">{{ number_format($approvedRow->count ?? 0) }} Invoices</td>
                <td class="text-end fw-bold" style="color: #15803d;">৳ {{ number_format($approvedRow->amount ?? 0, 2) }}</td>
                <td class="text-end">{{ $approvedRow->share_percent ?? 0 }}%</td>
                <td style="color: #15803d; font-size: 9.5px;">Cleared in Inventory</td>
              </tr>
              {{-- Pending Review --}}
              <tr>
                <td><span class="doc-badge doc-badge-pending">Pending Review</span> Awaiting administrative approval</td>
                <td class="text-center">{{ number_format($pendingRow->count ?? 0) }} Invoices</td>
                <td class="text-end fw-bold" style="color: #b45309;">৳ {{ number_format($pendingRow->amount ?? 0, 2) }}</td>
                <td class="text-end">{{ $pendingRow->share_percent ?? 0 }}%</td>
                <td style="color: #b45309; font-size: 9.5px;">Awaiting Admin Review</td>
              </tr>
              {{-- Rejected --}}
              <tr>
                <td><span class="doc-badge doc-badge-rejected">Rejected / Cancelled</span> Voided requests with no inventory impact</td>
                <td class="text-center">{{ number_format($rejectedRow->count ?? 0) }} Invoices</td>
                <td class="text-end fw-bold" style="color: #b91c1c;">৳ {{ number_format($rejectedRow->amount ?? 0, 2) }}</td>
                <td class="text-end">{{ $rejectedRow->share_percent ?? 0 }}%</td>
                <td style="color: #b91c1c; font-size: 9.5px;">Cancelled / Void</td>
              </tr>
              {{-- Stock Cuts / Returns Deduction --}}
              @if($totalStockCutAmount > 0)
                <tr style="background: #fff1f2;">
                  <td><strong>Approved Stock Cuts / Purchase Returns Deduction</strong></td>
                  <td class="text-center">{{ number_format($totalStockCutCount) }} Returns</td>
                  <td class="text-end fw-bold text-danger">- ৳ {{ number_format($totalStockCutAmount, 2) }}</td>
                  <td class="text-end">{{ $totalProcurementValue > 0 ? round(($totalStockCutAmount / $totalProcurementValue) * 100, 1) : 0 }}%</td>
                  <td style="color: #b91c1c; font-size: 9.5px;">Supplier Balance Credited</td>
                </tr>
              @endif
            </tbody>
            <tfoot>
              <tr>
                <td>FINAL REALIZED PROCUREMENT COST (After Returns):</td>
                <td class="text-center">{{ number_format($totalRequestsCount) }} Invoices</td>
                <td class="text-end" style="color: #059669; font-size: 11.5px;">৳ {{ number_format($netRealizedProcurement, 2) }}</td>
                <td class="text-end">{{ $totalProcurementValue > 0 ? round(($netRealizedProcurement / $totalProcurementValue) * 100, 1) : 0 }}%</td>
                <td style="color: #059669; font-size: 9.5px;">Official Realized Net</td>
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
            <p>Procurement &amp; Audit</p>
            <small>Internal Audit Department</small>
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
            Report Code: <span style="font-family: monospace;">SR-SKIN-{{ $startDate->format('ymd') }}-{{ $endDate->format('ymd') }}</span> | Consolidated Statement
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
      filename:     'Executive_Stock_In_Report_{{ $startDate->format('Ymd') }}_to_{{ $endDate->format('Ymd') }}.pdf',
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
      pagebreak:    { mode: ['css', 'legacy'] }
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

