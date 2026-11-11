@extends('layouts.adminlayout')

@section('content')
<style>
  .settings-header {
    margin-bottom: 2rem;
  }

  .settings-header h2 {
    font-family: 'Cinzel', serif;
    font-size: 24px;
    color: var(--text-main);
  }

  .settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }

  /* Professional Setting Card */
  .setting-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1.2rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none !important;
    position: relative;
    overflow: hidden;
  }

  .setting-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary);
    box-shadow: 0 10px 25px var(--glass);
  }

  .setting-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
  }

  .setting-card:hover .setting-icon {
    background: var(--primary);
    color: #ffffff;
  }

  .setting-content h5 {
    color: var(--text-main);
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .setting-content p {
    color: var(--text-muted);
    font-size: 13px;
    margin-bottom: 0;
    line-height: 1.4;
  }

  .arrow-link {
    position: absolute;
    right: 1.5rem;
    top: 1.5rem;
    color: var(--border-color);
    font-size: 12px;
    transition: all 0.3s ease;
  }

  .setting-card:hover .arrow-link {
    color: var(--primary);
    transform: translateX(5px);
  }

  @media (max-width: 576px) {
    .settings-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="container-fluid p-0">
  <div class="settings-header">
    <h2>Company Reports</h2>
    <p style="color: var(--text-muted); font-size: 14px;">Analyze performance, transactions, stock, and financial stats across all branches.</p>
  </div>

  <div class="settings-grid">

    <a href="{{ route('admin.report.sales') }}" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="setting-content">
        <h5>Sales Report</h5>
        <p>Track multi-branch sales performance, revenue trends, and top-selling products.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="{{ route('admin.report.stock-in') }}" class="setting-card">
      <div class="setting-icon">
        <div class="position-relative">
          <i class="fas fa-shopping-cart"></i>
        </div>
      </div>
      <div class="setting-content">
        <h5>Stock-In Report</h5>
        <p>Review procurement records, supplier invoice transactions, and purchase costs.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="{{ route('admin.report.inventory') }}" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-boxes-stacked"></i>
      </div>
      <div class="setting-content">
        <h5>Inventory Report</h5>
        <p>Monitor current branch stocks, detailed stock movement logs, low alerts, and waste reports.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="{{ route('admin.report.financial') }}" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>
      <div class="setting-content">
        <h5>Financial Report</h5>
        <p>Analyze income statements (Profit &amp; Loss), transaction registers, and cash flow trends.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    

    <a href="{{ route('admin.report.branch-performance') }}" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="setting-content">
        <h5>Branch Performance</h5>
        <p>Compare branch performance metrics side-by-side: Sales, Profit, Expenses, and Stock values.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="{{ route('admin.report.sr-performance') }}" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-user-tie"></i>
      </div>
      <div class="setting-content">
        <h5>SR Performance</h5>
        <p>Rank field representatives by orders, revenue, collection rate, and customer activity.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="{{ route('admin.report.dso-performance') }}" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-truck-fast"></i>
      </div>
      <div class="setting-content">
        <h5>DSO Performance</h5>
        <p>Rank Delivery Sales Officers (DSO) by successful deliveries, delivery volume, and cash collection.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

  </div>
</div>
@endsection


