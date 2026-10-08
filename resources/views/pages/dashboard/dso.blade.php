@extends('layouts.dsolayout')

@section('content')
<style>
  /* ── Quick-Action Grid ───────────────────────────────────────── */
  .quick-access-card {
    background: none;
    border: none;
    margin-bottom: 20px;
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

  .icon-item:hover { transform: translateY(-4px); }

  .icon-circle {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
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
    background: rgba(255,255,255,.18);
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
    max-width: 120px;
    line-height: 1.3;
  }

  .bg-delivery  { background: linear-gradient(135deg, #0202e2, #00b4d8); color: #fff; }
  .bg-delivered { background: linear-gradient(135deg, #0ba360, #3cba92); color: #fff; }
  .bg-search    { background: linear-gradient(135deg, #0ba360, #3cba92); color: #fff; }

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
    padding: 18px 16px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
    transition: transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
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
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.09);
  }

  .dso-stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    margin-bottom: 12px;
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
    letter-spacing: .7px;
    color: var(--text-muted);
    margin-bottom: 5px;
    line-height: 1.3;
  }

  .dso-stat-value {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.1;
    letter-spacing: -.5px;
    margin-bottom: 0;
  }

  .dso-stat-sub {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 6px;
    margin-bottom: 0;
    line-height: 1.25;
  }

  /* ── Chart Card ───────────────────────────────────────────────── */
  .dso-chart-card {
    background: var(--section-bg);
    border-radius: 14px;
    padding: 20px 18px 12px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
  }

  .chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 16px;
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
    margin-bottom: 0;
  }

  .chart-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 50px;
    background: rgba(102,126,234,.1);
    color: #667eea;
    white-space: nowrap;
  }

  /* ── Mobile Optimization (2 Cards per row) ─────────────────────── */
  @media (max-width: 768px) {
    .dso-header-title {
      font-size: 18px !important;
    }

    .dso-header-sub {
      font-size: 11.5px !important;
    }

    .dso-date-badge {
      font-size: 10.5px !important;
      padding: 4px 10px !important;
    }

    /* Quick Access */
    .icon-grid {
      gap: 20px !important;
    }

    .icon-circle {
      width: 52px !important;
      height: 52px !important;
      font-size: 20px !important;
      margin-bottom: 6px !important;
    }

    .icon-item span {
      font-size: 10px !important;
      letter-spacing: .3px !important;
      max-width: 95px !important;
    }

    /* Stat Cards (2 per line) */
    .dso-stat-grid {
      grid-template-columns: repeat(2, 1fr) !important;
      gap: 10px !important;
      margin-bottom: 16px !important;
    }

    .dso-stat-card {
      padding: 12px 10px !important;
      border-radius: 12px !important;
    }

    .dso-stat-card::before {
      height: 2.5px !important;
      border-radius: 12px 12px 0 0 !important;
    }

    .dso-stat-icon {
      width: 32px !important;
      height: 32px !important;
      font-size: 14px !important;
      border-radius: 8px !important;
      margin-bottom: 6px !important;
    }

    .dso-stat-label {
      font-size: 9px !important;
      letter-spacing: .3px !important;
      margin-bottom: 3px !important;
      line-height: 1.2 !important;
    }

    .dso-stat-value {
      font-size: 17px !important;
      letter-spacing: -.4px !important;
      line-height: 1.1 !important;
    }

    .dso-stat-sub {
      font-size: 8.5px !important;
      margin-top: 3px !important;
      line-height: 1.2 !important;
    }

    .dso-stat-sub i {
      font-size: 8px !important;
    }

    /* Chart */
    .dso-chart-card {
      padding: 14px 10px 8px !important;
      border-radius: 12px !important;
    }

    .chart-title {
      font-size: 12px !important;
    }

    .chart-subtitle {
      font-size: 9.5px !important;
    }

    .chart-badge {
      font-size: 9.5px !important;
      padding: 3px 8px !important;
    }
  }

  @media (max-width: 380px) {
    .dso-stat-grid {
      gap: 8px !important;
    }

    .dso-stat-card {
      padding: 10px 8px !important;
    }

    .dso-stat-icon {
      width: 28px !important;
      height: 28px !important;
      font-size: 12px !important;
    }

    .dso-stat-label {
      font-size: 8.5px !important;
    }

    .dso-stat-value {
      font-size: 15px !important;
    }

    .dso-stat-sub {
      font-size: 8px !important;
    }
  }
</style>

{{-- ── Header ───────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2 animate__animated animate__fadeIn">
  <div>
    <h2 class="dso-header-title" style="font-size: 22px; font-family: 'Cinzel', serif; font-weight: 700; color: var(--primary); margin: 0;">DSO Delivery Dashboard</h2>
    <p class="text-muted small m-0 dso-header-sub">Welcome back, <strong>{{ auth()->user()->fullname ?? auth()->user()->username }}</strong> </p>
  </div>
  <span class="badge p-2 px-3 dso-date-badge"
        style="letter-spacing:1px;color:var(--text-muted);background:var(--section-bg);border-radius:50px;border:1px solid var(--border-color);">
    <i class="far fa-calendar-alt me-1"></i> {{ date('d M, Y') }}
  </span>
</div>

{{-- ── Quick-Action Buttons ────────────────────────────────────── --}}
<div class="quick-access-card animate__animated animate__fadeInUp">
  <div class="icon-grid">
    <a href="{{ route('dso.order.delivery.index') }}" class="icon-item">
      <div class="icon-circle bg-delivery">
        <i class="fas fa-truck-ramp-box"></i>
      </div>
      <span>Find & Deliver Orders</span>
    </a>
    <a href="{{ route('dso.order.delivered_list') }}" class="icon-item">
      <div class="icon-circle bg-delivered">
        <i class="fas fa-clipboard-check"></i>
      </div>
      <span>Already Delivered</span>
    </a>
  </div>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────────── --}}
<div class="dso-stat-grid animate__animated animate__fadeInUp" style="animation-delay:.1s;">

  {{-- Card 1: Lifetime Delivered Orders Count --}}
  <div class="dso-stat-card card-delivered-amt">
    <div class="dso-stat-icon icon-amt">
      <i class="fas fa-boxes-stacked"></i>
    </div>
    <p class="dso-stat-label">Total Delivered in Life</p>
    <p class="dso-stat-value">{{ number_format($dsoLifetimeDeliveredCount ?? 0) }}</p>
    <p class="dso-stat-sub"><i class="fas fa-circle-info me-1" style="color:#667eea;"></i>Lifetime delivered orders</p>
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
      <p class="chart-subtitle">Delivered orders per day for {{ date('F Y') }}</p>
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
    const raw    = @json($dsoDailyChart ?? []);
    const days   = raw.map(r => r.day);
    const orders = raw.map(r => parseInt(r.orders ?? r.sales ?? 0));

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const labelColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor  = isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.05)';

    const isMobile = window.innerWidth <= 768;
    const options = {
      series: [{ name: 'Delivered Orders', data: orders }],
      chart: {
        type: 'area',
        height: isMobile ? 180 : 230,
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
        hover: { size: 4, colors: ['#0202e2'], strokeColors: '#fff', strokeWidth: 2 },
      },
      xaxis: {
        categories: days,
        title: { text: 'Day of Month', style: { color: labelColor, fontSize: isMobile ? '9.5px' : '11px' } },
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: labelColor, fontSize: isMobile ? '9.5px' : '11px' } },
      },
      yaxis: {
        min: 0,
        forceNiceScale: true,
        labels: {
          formatter: v => Math.floor(v),
          style: { colors: labelColor, fontSize: isMobile ? '9.5px' : '11px' },
        },
      },
      tooltip: {
        theme: isDark ? 'dark' : 'light',
        y: { formatter: v => v + ' Orders' },
      },
      grid: {
        borderColor: gridColor,
        strokeDashArray: 4,
        padding: { left: 4, right: 4, top: 0, bottom: 0 },
      },
    };

    new ApexCharts(document.querySelector('#dsoDailyChart'), options).render();
  });
</script>
@endpush
