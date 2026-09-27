@extends('layouts.dsolayout')

@section('content')
<style>
  /* ── Quick-Action Grid ───────────────────────────────────────── */
  .quick-access-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 14px;
    padding: 20px 15px;
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
    margin-bottom: 22px;
  }

  .icon-grid {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
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
    width: 65px;
    height: 65px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 14px rgba(0,0,0,.12);
    position: relative;
    overflow: hidden;
  }

  .icon-circle::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,.15);
    opacity: 0;
    transition: opacity .3s ease;
  }

  .icon-item:hover .icon-circle::after { opacity: 1; }

  .icon-item span {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-main);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .5px;
  }

  .bg-delivery { background: linear-gradient(135deg, #0202e2, #00b4d8); color: #fff; }
  .bg-search   { background: linear-gradient(135deg, #0ba360, #3cba92); color: #fff; }

  /* ── Stat Cards ──────────────────────────────────────────────── */
  .dso-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 22px;
  }

  .dso-stat-card {
    background: var(--section-bg);
    border-radius: 14px;
    padding: 20px 18px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
    transition: transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s ease;
    position: relative;
    overflow: hidden;
  }

  .dso-stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 14px 14px 0 0;
  }

  .card-delivered-amt::before { background: linear-gradient(90deg, #667eea, #764ba2); }
  .card-delivered-qty::before { background: linear-gradient(90deg, #0ba360, #3cba92); }
  .card-today::before         { background: linear-gradient(90deg, #f093fb, #f5576c); }
  .card-pending::before       { background: linear-gradient(90deg, #f6d365, #fda085); }

  .dso-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(0,0,0,.1);
  }

  .dso-stat-icon {
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

  .icon-amt     { background: rgba(102,126,234,.12); color: #667eea; }
  .icon-qty     { background: rgba(11,163,96,.12);   color: #0ba360; }
  .icon-today   { background: rgba(240,147,251,.12); color: #f5576c; }
  .icon-pending { background: rgba(246,211,101,.15); color: #e67e22; }

  .dso-stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--text-muted);
    margin-bottom: 6px;
  }

  .dso-stat-value {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.1;
    letter-spacing: -.5px;
  }

  .dso-stat-sub {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 6px;
  }

  /* ── Chart Card ───────────────────────────────────────────────── */
  .dso-chart-card {
    background: var(--section-bg);
    border-radius: 14px;
    padding: 22px 20px 14px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
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
    text-transform: uppercase;
    letter-spacing: .6px;
    margin: 0;
  }

  .chart-subtitle {
    font-size: 11.5px;
    color: var(--text-muted);
    margin-top: 3px;
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
    white-space: nowrap;
  }
</style>

{{-- ── Header ───────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4 animate__animated animate__fadeIn">
  <div>
    <h2 style="font-size: 22px; font-family: 'Cinzel', serif; font-weight: 700; color: var(--primary); margin: 0;">DSO Delivery Dashboard</h2>
    <p class="text-muted small m-0">Welcome back, <strong>{{ auth()->user()->fullname ?? auth()->user()->username }}</strong> ({{ auth()->user()->branch?->name ?? 'Main Branch' }})</p>
  </div>
  <span class="badge p-2 px-3"
        style="letter-spacing:1px;color:var(--text-muted);background:var(--section-bg);border-radius:50px;border:1px solid var(--border-color);">
    <i class="far fa-calendar-alt me-1"></i> {{ date('d M, Y') }}
  </span>
</div>

{{-- ── Quick-Action Delivery Button ─────────────────────────────── --}}
<div class="quick-access-card animate__animated animate__fadeInUp">
  <div class="icon-grid">
    <a href="{{ route('dso.order.delivery.index') }}" class="icon-item">
      <div class="icon-circle bg-delivery">
        <i class="fas fa-truck-ramp-box"></i>
      </div>
      <span>Find & Deliver Orders</span>
    </a>
  </div>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────────── --}}
<div class="dso-stat-grid animate__animated animate__fadeInUp" style="animation-delay:.1s;">

  {{-- Card 1: Monthly Delivered Amount --}}
  <div class="dso-stat-card card-delivered-amt">
    <div class="dso-stat-icon icon-amt">
      <i class="fas fa-sack-dollar"></i>
    </div>
    <p class="dso-stat-label">{{ $monthName ?? date('F') }} Delivery Volume</p>
    <p class="dso-stat-value">৳ {{ number_format($dsoMonthlyDeliveredAmount ?? 0, 0) }}</p>
    <p class="dso-stat-sub"><i class="fas fa-circle-info me-1" style="color:#667eea;"></i>Total value delivered this month</p>
  </div>

  {{-- Card 2: Monthly Delivered Orders Count --}}
  <div class="dso-stat-card card-delivered-qty">
    <div class="dso-stat-icon icon-qty">
      <i class="fas fa-circle-check"></i>
    </div>
    <p class="dso-stat-label">Monthly Deliveries</p>
    <p class="dso-stat-value">{{ number_format($dsoMonthlyDeliveredCount ?? 0) }}</p>
    <p class="dso-stat-sub"><i class="fas fa-circle-info me-1" style="color:#0ba360;"></i>Orders successfully delivered</p>
  </div>

  {{-- Card 3: Today's Deliveries --}}
  <div class="dso-stat-card card-today">
    <div class="dso-stat-icon icon-today">
      <i class="fas fa-calendar-day"></i>
    </div>
    <p class="dso-stat-label">Today's Delivered</p>
    <p class="dso-stat-value">{{ number_format($dsoTodayDeliveredCount ?? 0) }}</p>
    <p class="dso-stat-sub"><i class="fas fa-circle-info me-1" style="color:#f5576c;"></i>Delivered by you today</p>
  </div>

  {{-- Card 4: Branch Ready Deliveries --}}
  <div class="dso-stat-card card-pending">
    <div class="dso-stat-icon icon-pending">
      <i class="fas fa-boxes-packing"></i>
    </div>
    <p class="dso-stat-label">Ready in Branch</p>
    <p class="dso-stat-value">{{ number_format($branchPendingDeliveryCount ?? 0) }}</p>
    <p class="dso-stat-sub"><i class="fas fa-circle-info me-1" style="color:#e67e22;"></i>Approved / Shipped orders</p>
  </div>

</div>

{{-- ── Daily Delivery Chart ─────────────────────────────────────── --}}
<div class="dso-chart-card animate__animated animate__fadeInUp" style="animation-delay:.2s;">
  <div class="chart-header">
    <div>
      <p class="chart-title">Daily Deliveries — {{ $monthName ?? date('F') }}</p>
      <p class="chart-subtitle">Delivered order amount per day for {{ date('F Y') }}</p>
    </div>
    <span class="chart-badge">
      <i class="fas fa-chart-area"></i> Real-time Deliveries
    </span>
  </div>
  <div id="dsoDailyChart" style="min-height:230px;"></div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const raw   = @json($dsoDailyChart ?? []);
    const days  = raw.map(r => r.day);
    const sales = raw.map(r => parseFloat(r.sales));

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const labelColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor  = isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.05)';

    const options = {
      series: [{ name: 'Delivered Sales (৳)', data: sales }],
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
      stroke: { curve: 'smooth', width: 2.5, colors: ['#0202e2'] },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          colorStops: [
            { offset: 0,   color: '#0202e2', opacity: 0.35 },
            { offset: 100, color: '#0202e2', opacity: 0.02 },
          ],
        },
      },
      markers: {
        size: 0,
        hover: { size: 5, colors: ['#0202e2'], strokeColors: '#fff', strokeWidth: 2 },
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

    new ApexCharts(document.querySelector('#dsoDailyChart'), options).render();
  });
</script>
@endpush
