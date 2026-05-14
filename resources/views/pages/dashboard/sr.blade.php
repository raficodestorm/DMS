@extends('layouts.srlayout')

@section('content')
<style>
  /* ── Quick-Action Grid ───────────────────────────────────────── */
  .quick-access-card {
    border-radius: 15px;
    padding: 15px 10px;
  }

  .icon-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px 10px;
  }

  .icon-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .icon-item:hover { transform: translateY(-5px); }

  .icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,.1);
    position: relative;
    overflow: hidden;
  }

  .icon-circle::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,.12);
    opacity: 0;
    transition: opacity .3s ease;
  }

  .icon-item:hover .icon-circle::after { opacity: 1; }

  .icon-item span {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-main);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-family: 'Inter', sans-serif;
  }

  .bg-order      { background: linear-gradient(135deg,#667eea,#764ba2); color:#fff; }
  .bg-customer   { background: linear-gradient(135deg,#0ba360,#3cba92); color:#fff; }
  .bg-account    { background: linear-gradient(135deg,#f093fb,#f5576c); color:#fff; }
  .bg-payment    { background: linear-gradient(135deg,#f6d365,#fda085); color:#fff; }
  .bg-return     { background: linear-gradient(135deg,#ff0844,#ffb199); color:#fff; }
  .bg-all-orders { background: linear-gradient(135deg,#4facfe,#00f2fe); color:#fff; }

  /* ── Stat Cards ──────────────────────────────────────────────── */
  .sr-stat-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 22px;
  }

  .sr-stat-card {
    background: var(--section-bg);
    border-radius: 14px;
    padding: 20px 18px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
    transition: transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s ease;
    overflow: hidden;
    position: relative;
  }

  .sr-stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 14px 14px 0 0;
  }

  .sr-stat-card.card-orders::before  { background: linear-gradient(90deg,#667eea,#764ba2); }
  .sr-stat-card.card-customers::before { background: linear-gradient(90deg,#0ba360,#3cba92); }

  .sr-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(0,0,0,.1);
  }

  .sr-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 14px;
    flex-shrink: 0;
  }

  .icon-orders    { background: rgba(102,126,234,.12); color: #667eea; }
  .icon-customers { background: rgba(11,163,96,.12);  color: #0ba360; }

  .sr-stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--text-muted);
    margin-bottom: 6px;
    font-family: 'Inter', sans-serif;
  }

  .sr-stat-value {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-main);
    font-family: 'Inter', sans-serif;
    line-height: 1;
    letter-spacing: -.5px;
  }

  .sr-stat-sub {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 6px;
    font-family: 'Inter', sans-serif;
  }

  /* ── Chart Card ───────────────────────────────────────────────── */
  .sr-chart-card {
    background: var(--section-bg);
    border-radius: 14px;
    padding: 22px 20px 14px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
    margin-top: 22px;
  }

  .chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 18px;
    flex-wrap: wrap;
    gap: 8px;
  }

  .chart-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-main);
    font-family: 'Inter', sans-serif;
    text-transform: uppercase;
    letter-spacing: .6px;
  }

  .chart-subtitle {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 3px;
    font-family: 'Inter', sans-serif;
  }

  .chart-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 50px;
    background: rgba(102,126,234,.1);
    color: #667eea;
    font-family: 'Inter', sans-serif;
    white-space: nowrap;
  }

  /* ── Responsive ───────────────────────────────────────────────── */
  @media (max-width: 480px) {
    .icon-circle { width: 50px; height: 50px; font-size: 19px; }
    .icon-item span { font-size: 9px; }
    .icon-grid { gap: 28px 6px; }
    .sr-stat-value { font-size: 18px; }
    .sr-stat-card { padding: 16px 14px; }
  }
</style>

{{-- ── Header ───────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
  <h2 style="font-size: 22px; font-family: 'Cinzel', serif; font-weight: 700; color: var(--primary);">Overview</h2>
  <span class="badge p-2 px-3"
        style="letter-spacing:1px;color:var(--text-muted);background:var(--section-bg);border-radius:50px;border:1px solid var(--border-color);">
    <i class="far fa-calendar-alt me-1"></i> {{ date('d M, Y') }}
  </span>
</div>

{{-- ── Quick-Action Icons ──────────────────────────────────────── --}}
<div class="quick-access-card animate__animated animate__fadeInUp">
  <div class="icon-grid">

    <a href="{{ route('sr.order.create') }}" class="icon-item">
      <div class="icon-circle bg-order"><i class="fas fa-cart-plus"></i></div>
      <span>Order</span>
    </a>

    <a href="{{ route('customers.create') }}" class="icon-item">
      <div class="icon-circle bg-customer"><i class="fas fa-user-plus"></i></div>
      <span>Add Customer</span>
    </a>

    <a href="{{ route('sr.users.create') }}" class="icon-item">
      <div class="icon-circle bg-account"><i class="fas fa-user-gear"></i></div>
      <span>Add Account</span>
    </a>

    <a href="{{ route('sr.payments.create') }}" class="icon-item">
      <div class="icon-circle bg-payment"><i class="fas fa-money-bill-wave"></i></div>
      <span>Payment</span>
    </a>

    <a href="{{ route('sr.return.create') }}" class="icon-item">
      <div class="icon-circle bg-return"><i class="fas fa-rotate-left"></i></div>
      <span>Return</span>
    </a>

    <a href="{{ route('sr.order.all') }}" class="icon-item">
      <div class="icon-circle bg-all-orders"><i class="fas fa-list-check"></i></div>
      <span>All Orders</span>
    </a>

  </div>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────────── --}}
<div class="sr-stat-row animate__animated animate__fadeInUp" style="animation-delay:.1s;">

  {{-- Card 1: Monthly Order Amount --}}
  <div class="sr-stat-card card-orders">
    <div class="sr-stat-icon icon-orders">
      <i class="fas fa-receipt"></i>
    </div>
    <p class="sr-stat-label">{{ $monthName }} Orders</p>
    <p class="sr-stat-value">৳ {{ number_format($srMonthlyOrderAmount, 0) }}</p>
    <p class="sr-stat-sub"><i class="fas fa-circle-info me-1" style="color:#667eea;"></i>Your orders this month</p>
  </div>

  {{-- Card 2: Branch Customers --}}
  <div class="sr-stat-card card-customers">
    <div class="sr-stat-icon icon-customers">
      <i class="fas fa-store"></i>
    </div>
    <p class="sr-stat-label">Branch Customers</p>
    <p class="sr-stat-value">{{ number_format($branchCustomerCount) }}</p>
    <p class="sr-stat-sub"><i class="fas fa-circle-info me-1" style="color:#0ba360;"></i>Total in your branch</p>
  </div>

</div>

{{-- ── Daily Sales Chart ───────────────────────────────────────── --}}
<div class="sr-chart-card animate__animated animate__fadeInUp" style="animation-delay:.2s;">
  <div class="chart-header">
    <div>
      <p class="chart-title">Daily Sales — {{ $monthName }}</p>
      <p class="chart-subtitle">Your order amounts per day for {{ date('F Y') }}</p>
    </div>
    <span class="chart-badge">
      <i class="fas fa-chart-area"></i> Live Data
    </span>
  </div>
  <div id="srDailyChart" style="min-height:230px;"></div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const raw   = @json($srDailyChart);
    const days  = raw.map(r => r.day);
    const sales = raw.map(r => parseFloat(r.sales));

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const labelColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor  = isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.05)';

    const options = {
      series: [{ name: 'Sales (৳)', data: sales }],
      chart: {
        type: 'area',
        height: 230,
        toolbar: { show: false },
        zoom: { enabled: false },
        fontFamily: "'Inter', sans-serif",
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 700,
        },
        background: 'transparent',
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2.5, colors: ['#667eea'] },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          colorStops: [
            { offset: 0,   color: '#667eea', opacity: 0.35 },
            { offset: 100, color: '#667eea', opacity: 0.02 },
          ],
        },
      },
      markers: {
        size: 0,
        hover: { size: 5, colors: ['#667eea'], strokeColors: '#fff', strokeWidth: 2 },
      },
      xaxis: {
        categories: days,
        title: { text: 'Day of Month', style: { color: labelColor, fontSize: '11px' } },
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: labelColor, fontSize: '11px' } },
      },
      yaxis: {
        labels: {
          formatter: v => '৳' + v.toLocaleString(),
          style: { colors: labelColor, fontSize: '11px' },
        },
      },
      tooltip: {
        theme: isDark ? 'dark' : 'light',
        y: { formatter: v => '৳ ' + v.toLocaleString() },
      },
      grid: {
        borderColor: gridColor,
        strokeDashArray: 4,
        padding: { left: 8, right: 8, top: 0, bottom: 0 },
      },
    };

    new ApexCharts(document.querySelector('#srDailyChart'), options).render();
  });
</script>
@endpush