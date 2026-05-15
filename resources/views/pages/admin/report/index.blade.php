@extends('layouts.adminlayout')

@section('content')
<style>
    /* ── Top Header & Filter ────────────────────────────────────────── */
    .report-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .report-title {
        font-family: 'Cinzel', serif;
        font-size: 24px;
        font-weight: 700;
        color: var(--primary);
        margin: 0;
    }
    .filter-card {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .filter-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 150px;
    }
    .filter-group label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 6px;
        font-weight: 600;
    }
    .filter-input {
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 13px;
        transition: all 0.3s;
    }
    .filter-input:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
    }
    
    /* ── Summary Cards ──────────────────────────────────────────────── */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .summary-card {
        background: var(--section-bg);
        border-radius: 12px;
        padding: 20px;
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: transform 0.3s ease;
    }
    .summary-card:hover {
        transform: translateY(-5px);
    }
    .summary-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
    }
    .sc-sales::before { background: linear-gradient(90deg, #4e73df, #224abe); }
    .sc-profit::before { background: linear-gradient(90deg, #1cc88a, #13855c); }
    .sc-due::before { background: linear-gradient(90deg, #e74a3b, #be2617); }
    .sc-cost::before { background: linear-gradient(90deg, #f6c23e, #dda20a); }
    .sc-stock::before { background: linear-gradient(90deg, #36b9cc, #258391); }
    
    .sc-title {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 8px;
    }
    .sc-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 5px;
    }
    .sc-sub {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* ── Complex Layout Grid ────────────────────────────────────────── */
    .report-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 30px;
    }
    @media (max-width: 991px) {
        .report-grid { grid-template-columns: 1fr; }
    }
    
    .report-section {
        background: var(--section-bg);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .rs-header {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(0,0,0,0.02);
    }
    .rs-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rs-body {
        padding: 20px;
        flex: 1;
    }
    
    /* ── Metric List ────────────────────────────────────────────────── */
    .metric-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .metric-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed var(--border-color);
    }
    .metric-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .metric-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .metric-val {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
    }
    .val-success { color: #1cc88a; }
    .val-danger { color: #e74a3b; }
    .val-primary { color: #4e73df; }
    
    /* ── Profit Waterfall Structure ─────────────────────────────────── */
    .waterfall {
        background: var(--bg-color);
        border-radius: 8px;
        padding: 15px;
        border: 1px solid var(--border-color);
    }
    .wf-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }
    .wf-row.main {
        font-weight: 700;
        color: var(--text-main);
        font-size: 15px;
        border-top: 1px solid var(--border-color);
        padding-top: 12px;
        margin-top: 4px;
    }
    .wf-row.sub {
        padding-left: 15px;
        color: var(--text-muted);
        font-size: 13px;
        position: relative;
    }
    .wf-row.sub::before {
        content: '↳';
        position: absolute;
        left: 0;
        color: var(--border-color);
    }
    
    .btn-export {
        background: var(--primary);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 6px rgba(78, 115, 223, 0.2);
    }
    .btn-export:hover {
        background: #2e59d9;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
    }

    /* ── Print & Download PDF Styles ────────────────────────────────── */
    @media print {
        body { background: #fff !important; color: #000 !important; }
        .sidebar, .custom-navbar, .filter-card, .btn-export, .theme-toggle { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .report-header { justify-content: center; margin-bottom: 20px; }
        .report-title { color: #000 !important; font-size: 28px; }
        .badge { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
        .summary-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 15px; page-break-inside: avoid; }
        .summary-card { break-inside: avoid; border: 1px solid #ccc !important; box-shadow: none !important; }
        .summary-card::before { display: none !important; }
        .sc-value { color: #000 !important; }
        .report-grid { display: block; }
        .report-section { break-inside: avoid; page-break-inside: avoid; margin-bottom: 20px; border: 1px solid #ddd !important; box-shadow: none !important; }
        .rs-header { background: #f8f9fc !important; border-bottom: 1px solid #ddd !important; -webkit-print-color-adjust: exact; }
        .waterfall { border: none; padding: 0; }
        .wf-row.main { border-top: 1px solid #000; }
        .metric-item { border-bottom: 1px solid #eee; }
        #salesChart { display: none !important; } /* Hide interactive chart in print for cleaner data table look, or optionally keep it if it renders well */
        .val-success, .val-danger, .val-primary, .text-primary, .text-success, .text-danger, .text-info, .text-warning { color: #000 !important; }
    }
</style>

<div class="report-header animate__animated animate__fadeIn">
    <h2 class="report-title"><i class="fas fa-chart-pie me-2"></i> Business Report</h2>
    <div class="d-flex gap-2">
        <span class="badge bg-primary text-white p-2 px-3" style="font-size: 13px;">
            <i class="far fa-calendar-alt me-1"></i> {{ $periodLabel }}
        </span>
        <button onclick="window.print()" class="btn-export">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </button>
    </div>
</div>

<!-- FILTER SECTION -->
<div class="filter-card animate__animated animate__fadeInUp">
    <form action="{{ route('admin.report.index') }}" method="GET" class="filter-form">
        <div class="filter-group">
            <label>Start Date</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-input">
        </div>
        <div class="filter-group">
            <label>End Date</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-input">
        </div>
        
        <div class="filter-group" style="align-items: center; flex-direction: row; gap: 10px; min-width: auto; padding-bottom: 10px;">
            <span style="color: var(--text-muted); font-size: 12px; font-weight: 600;">OR</span>
        </div>

        <div class="filter-group">
            <label>Month</label>
            <select name="month" class="filter-input">
                <option value="">Select Month</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label>Year</label>
            <select name="year" class="filter-input">
                <option value="">Select Year</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="filter-group" style="flex: 0;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: 600;">Generate</button>
        </div>
        <div class="filter-group" style="flex: 0;">
            <a href="{{ route('admin.report.index') }}" class="btn btn-secondary" style="padding: 10px 20px; font-weight: 600;">Reset</a>
        </div>
    </form>
</div>

<!-- SUMMARY CARDS -->
<div class="summary-grid animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
    <div class="summary-card sc-stock">
        <div class="sc-title">Stock Value</div>
        <div class="sc-value text-info">৳ {{ number_format($totalStockValue, 2) }}</div>
        <div class="sc-sub"><i class="fas fa-warehouse me-1 text-info"></i> Current inventory asset</div>
    </div>
    <div class="summary-card sc-sales">
        <div class="sc-title">Total Sales</div>
        <div class="sc-value">৳ {{ number_format($totalSalesAmount, 2) }}</div>
        <div class="sc-sub"><i class="fas fa-shopping-bag me-1 text-primary"></i> {{ $totalOrders }} Orders</div>
    </div>
    
    <div class="summary-card sc-due">
        <div class="sc-title">Market Due</div>
        <div class="sc-value val-danger">৳ {{ number_format($totalDueAmount, 2) }}</div>
        <div class="sc-sub"><i class="fas fa-exclamation-triangle me-1 text-danger"></i> Total pending collection</div>
    </div>
    <div class="summary-card sc-cost">
        <div class="sc-title">Total Cost</div>
        <div class="sc-value">৳ {{ number_format($totalCost, 2) }}</div>
        <div class="sc-sub"><i class="fas fa-file-invoice-dollar me-1 text-warning"></i> Includes salary & office</div>
    </div>
    
    <div class="summary-card sc-profit">
        <div class="sc-title">Net Profit</div>
        <div class="sc-value val-success">৳ {{ number_format($netProfit, 2) }}</div>
        <div class="sc-sub"><i class="fas fa-chart-line me-1 text-success"></i> After costs & bonuses</div>
    </div>
</div>

<!-- REPORT GRID -->
<div class="report-grid animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
    
    <!-- Left Column -->
    <div class="d-flex flex-column gap-4">
        
        <!-- Profit Calculation Report -->
        <div class="report-section">
    <div class="rs-header">
        <h3 class="rs-title">
            <i class="fas fa-calculator text-primary"></i>
            Profit & Loss Statement
        </h3>
    </div>

    <div class="rs-body">
        <div class="waterfall">

            {{-- Revenue --}}
            <div class="wf-row">
                <span>Total Sales Revenue</span>
                <span class="val-primary">
                    ৳ {{ number_format($totalSalesAmount, 2) }}
                </span>
            </div>

            {{-- COGS --}}
            <div class="wf-row sub">
                <span>Less: Cost of Goods Sold (COGS)</span>
                <span class="val-danger">
                    - ৳ {{ number_format($totalCOGS, 2) }}
                </span>
            </div>

            {{-- Discount --}}
            <div class="wf-row sub">
                <span>Less: Discounts Provided</span>
                <span class="val-danger">
                    - ৳ {{ number_format($totalDiscount, 2) }}
                </span>
            </div>

            {{-- Gross Profit --}}
            <div class="wf-row main" style="border-top:none;padding-top:0;margin-top:0;">
                <span>
                    Gross Profit
                    <small class="text-muted">
                        (Revenue - COGS - Discount)
                    </small>
                </span>

                <span class="{{ $grossProfit >= 0 ? 'val-success' : 'val-danger' }}">
                    ৳ {{ number_format($grossProfit, 2) }}
                </span>
            </div>

            {{-- Bonus --}}
            <div class="wf-row sub mt-2">
                <span>Add: Bonuses Earned</span>

                <span class="val-success">
                    + ৳ {{ number_format($totalBonus, 2) }}
                </span>
            </div>

            {{-- Total Profit --}}
            <div class="wf-row main">
                <span>Total Profit</span>

                <span class="{{ $profit >= 0 ? 'val-success' : 'val-danger' }}">
                    ৳ {{ number_format($profit, 2) }}
                </span>
            </div>

            {{-- Operating Cost --}}
            <div class="wf-row sub mt-2">
                <span>Less: Operating Costs (Excl. Salary)</span>

                <span class="val-danger">
                    - ৳ {{ number_format($totalCost - $totalSalary, 2) }}
                </span>
            </div>

            {{-- Salary --}}
            <div class="wf-row sub">
                <span>Less: Salary Expenses</span>

                <span class="val-danger">
                    - ৳ {{ number_format($totalSalary, 2) }}
                </span>
            </div>

            {{-- Net Profit --}}
            <div class="wf-row main"
                 style="font-size:18px;color:var(--primary);padding-top:15px;margin-top:10px;">

                <span>Net Profit</span>

                <span class="{{ $netProfit >= 0 ? 'val-success' : 'val-danger' }}">
                    ৳ {{ number_format($netProfit, 2) }}
                </span>
            </div>

        </div>
    </div>
</div>

        <!-- Sales Chart -->
        <div class="report-section">
            <div class="rs-header">
                <h3 class="rs-title"><i class="fas fa-chart-area text-success"></i> Sales Trend</h3>
            </div>
            <div class="rs-body">
                <div id="salesChart" style="height: 250px;"></div>
            </div>
        </div>
        
    </div>

    <!-- Right Column -->
    <div class="d-flex flex-column gap-4">
        
        <!-- Sales & Transaction Breakdown -->
        <div class="report-section">
            <div class="rs-header">
                <h3 class="rs-title"><i class="fas fa-money-check-alt text-warning"></i> Transaction Breakdown</h3>
            </div>
            <div class="rs-body p-0">
                <ul class="metric-list" style="padding: 0 20px;">
                    <li class="metric-item">
                        <span class="metric-label">Total Product Sales</span>
                        <span class="metric-val">৳ {{ number_format($totalSalesAmount, 2) }}</span>
                    </li>
                    <li class="metric-item">
                        <span class="metric-label">Total Payment Received</span>
                        <span class="metric-val val-success">৳ {{ number_format($totalPaidAmount, 2) }}</span>
                    </li>
                    <li class="metric-item">
                        <span class="metric-label">Product Returns Amount</span>
                        <span class="metric-val val-danger">৳ {{ number_format($totalReturnedAmount, 2) }}</span>
                    </li>
                    <li class="metric-item">
                        <span class="metric-label">Total Buy Transactions</span>
                        <span class="metric-val">৳ {{ number_format($totalBuyTrans, 2) }}</span>
                    </li>
                    <li class="metric-item">
                        <span class="metric-label">Total Pay Transactions</span>
                        <span class="metric-val val-success">৳ {{ number_format($totalPayTrans, 2) }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="report-section">
            <div class="rs-header">
                <h3 class="rs-title"><i class="fas fa-box text-info"></i> Top Selling Products</h3>
            </div>
            <div class="rs-body p-0">
                <ul class="metric-list" style="padding: 0 20px;">
                    @forelse($mostSoldProducts as $item)
                        <li class="metric-item">
                            <span class="metric-label">{{ $item->product->name ?? 'Unknown' }}</span>
                            <span class="metric-val text-info">{{ $item->total_qty }} Units</span>
                        </li>
                    @empty
                        <li class="metric-item text-center">
                            <span class="metric-label w-100 py-3">No sales in this period</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Branch Performance -->
        <div class="report-section">
            <div class="rs-header">
                <h3 class="rs-title"><i class="fas fa-code-branch text-primary"></i> Branch Performance</h3>
            </div>
            <div class="rs-body p-0">
                <ul class="metric-list" style="padding: 0 20px;">
                    @forelse($branchSales as $branch)
                        <li class="metric-item">
                            <span class="metric-label">{{ $branch->branch_name }}</span>
                            <span class="metric-val">৳ {{ number_format($branch->total_sales, 2) }}</span>
                        </li>
                    @empty
                        <li class="metric-item text-center">
                            <span class="metric-label w-100 py-3">No branch data available</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

    const options = {
        series: [{
            name: 'Sales Amount',
            data: @json($chartSales)
        }],
        chart: {
            type: 'area',
            height: 250,
            toolbar: { show: false },
            fontFamily: "'Inter', sans-serif",
            background: 'transparent'
        },
        colors: ['#4e73df'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: @json($chartDates),
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: textColor, fontSize: '11px' } }
        },
        yaxis: {
            labels: {
                formatter: function (value) { return "৳ " + value.toLocaleString(); },
                style: { colors: textColor, fontSize: '11px' }
            }
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4,
            padding: { top: 0, right: 0, bottom: 0, left: 10 }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: function(value) { return "৳ " + value.toLocaleString(); } }
        }
    };

    new ApexCharts(document.querySelector("#salesChart"), options).render();
});
</script>
@endpush
@endsection
