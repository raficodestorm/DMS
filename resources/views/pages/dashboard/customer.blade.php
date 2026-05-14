@extends('layouts.customerlayout')

@section('content')
<style>
  /* ── Page Header ───────────────────────────────────────────────── */
  .cust-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
  }

  .cust-header h2 {
    font-size: 21px;
    font-family: 'Cinzel', serif;
    font-weight: 700;
    color: var(--primary);
    margin: 0;
  }

  .date-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-family: 'Inter', sans-serif;
    color: var(--text-muted);
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 50px;
    padding: 5px 14px;
  }

  /* ── Stat Grid ─────────────────────────────────────────────────── */
  .cust-stat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
    margin-bottom: 22px;
  }

  @media (min-width: 768px) {
    .cust-stat-grid { grid-template-columns: repeat(4, 1fr); }
  }

  /* ── Stat Card ─────────────────────────────────────────────────── */
  .cust-card {
    background: var(--section-bg);
    border-radius: 14px;
    padding: 18px 16px 16px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
    transition: transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s ease;
    position: relative;
    overflow: hidden;
  }

  .cust-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 14px 14px 0 0;
  }

  .cust-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,.1);
  }

  .cc-orders::before   { background: linear-gradient(90deg,#667eea,#764ba2); }
  .cc-payment::before  { background: linear-gradient(90deg,#0ba360,#3cba92); }
  .cc-due::before      { background: linear-gradient(90deg,#f59e0b,#ef4444); }
  .cc-status::before   { background: linear-gradient(90deg,#4facfe,#00f2fe); }

  .cust-card-icon {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 13px;
  }

  .ci-orders  { background: rgba(102,126,234,.12); color: #667eea; }
  .ci-payment { background: rgba(11,163,96,.12);   color: #0ba360; }
  .ci-due     { background: rgba(245,158,11,.12);  color: #f59e0b; }
  .ci-status  { background: rgba(79,172,254,.12);  color: #4facfe; }

  .cust-card-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--text-muted);
    font-family: 'Inter', sans-serif;
    margin-bottom: 5px;
  }

  .cust-card-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-main);
    font-family: 'Inter', sans-serif;
    line-height: 1.1;
    letter-spacing: -.4px;
  }

  .cust-card-sub {
    font-size: 10px;
    color: var(--text-muted);
    font-family: 'Inter', sans-serif;
    margin-top: 5px;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 50px;
    font-family: 'Inter', sans-serif;
    text-transform: capitalize;
  }

  .pill-pending_sr      { background: rgba(249,115,22,.12);  color: #f97316; }
  .pill-pending_manager { background: rgba(245,158,11,.12);  color: #f59e0b; }
  .pill-approved        { background: rgba(16,185,129,.12);  color: #10b981; }
  .pill-none            { background: rgba(100,116,139,.12); color: #64748b; }

  /* ── Chart Card ─────────────────────────────────────────────────── */
  .cust-chart-card {
    background: var(--section-bg);
    border-radius: 14px;
    padding: 22px 20px 14px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 18px rgba(0,0,0,.05);
  }

  .cust-chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 18px;
    flex-wrap: wrap;
    gap: 8px;
  }

  .cust-chart-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-main);
    font-family: 'Inter', sans-serif;
    text-transform: uppercase;
    letter-spacing: .6px;
  }

  .cust-chart-sub {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 3px;
    font-family: 'Inter', sans-serif;
  }

  .cust-chart-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 50px;
    background: rgba(79,172,254,.1);
    color: #4facfe;
    font-family: 'Inter', sans-serif;
    white-space: nowrap;
  }
</style>

<div class="cust-header animate__animated animate__fadeIn">
  <h2>My Dashboard</h2>
  <span class="date-badge">
    <i class="far fa-calendar-alt"></i> {{ date('d M, Y') }}
  </span>
</div>

<div class="cust-stat-grid">
  {{-- Card 1: Monthly Orders --}}
  <div class="cust-card cc-orders animate__animated animate__fadeInUp" style="animation-delay:.05s;">
    <div class="cust-card-icon ci-orders">
      <i class="fas fa-shopping-bag"></i>
    </div>
    <p class="cust-card-label">{{ $monthName }} Orders</p>
    <p class="cust-card-value">৳ {{ number_format($customerMonthlyOrders, 0) }}</p>
    <p class="cust-card-sub"><i class="fas fa-info-circle me-1" style="color:#667eea;"></i>Total order amount</p>
  </div>

  {{-- Card 2: Monthly Payments --}}
  <div class="cust-card cc-payment animate__animated animate__fadeInUp" style="animation-delay:.1s;">
    <div class="cust-card-icon ci-payment">
      <i class="fas fa-money-bill-wave"></i>
    </div>
    <p class="cust-card-label">{{ $monthName }} Payments</p>
    <p class="cust-card-value">৳ {{ number_format($customerMonthlyPayments, 0) }}</p>
    <p class="cust-card-sub"><i class="fas fa-info-circle me-1" style="color:#0ba360;"></i>Paid this month</p>
  </div>

  {{-- Card 3: Current Due --}}
  <div class="cust-card cc-due animate__animated animate__fadeInUp" style="animation-delay:.15s;">
    <div class="cust-card-icon ci-due">
      <i class="fas fa-file-invoice-dollar"></i>
    </div>
    <p class="cust-card-label">Outstanding Due</p>
    <p class="cust-card-value" style="{{ $customerCurrentDue > 0 ? 'color:#ef4444;' : 'color:#10b981;' }}">
      ৳ {{ number_format($customerCurrentDue, 0) }}
    </p>
    <p class="cust-card-sub"><i class="fas fa-info-circle me-1" style="color:#f59e0b;"></i>From latest record</p>
  </div>

  {{-- Card 4: Running Order Status --}}
  <div class="cust-card cc-status animate__animated animate__fadeInUp" style="animation-delay:.2s;">
    <div class="cust-card-icon ci-status">
      <i class="fas fa-truck-fast"></i>
    </div>
    <p class="cust-card-label">Running Order</p>
    @if($runningOrder)
      <p class="cust-card-value" style="font-size:14px; margin-bottom:6px;">
        #BRS{{ $runningOrder->id }}
      </p>
      @php
        $statusLabels = [
          'pending_sr'      => 'Pending SR',
          'pending_manager' => 'Pending Manager',
          'approved'        => 'Approved',
        ];
        $statusKey = $runningOrder->status;
        $statusLabel = $statusLabels[$statusKey] ?? $statusKey;
      @endphp
      <span class="status-pill pill-{{ $statusKey }}">
        <i class="fas fa-circle" style="font-size:6px;"></i>
        {{ $statusLabel }}
      </span>
    @else
      <p class="cust-card-value" style="font-size:15px; margin-bottom:6px;">—</p>
      <span class="status-pill pill-none">
        <i class="fas fa-circle" style="font-size:6px;"></i>
        No Active Order
      </span>
    @endif
    <p class="cust-card-sub mt-2"><i class="fas fa-info-circle me-1" style="color:#4facfe;"></i>In-progress status</p>
  </div>
</div>

<div class="cust-chart-card animate__animated animate__fadeInUp" style="animation-delay:.25s;">
  <div class="cust-chart-header">
    <div>
      <p class="cust-chart-title">Monthly Order History — {{ $monthName }}</p>
      <p class="cust-chart-sub">Daily purchase tracking for {{ date('F Y') }}</p>
    </div>
    <span class="cust-chart-badge">
      <i class="fas fa-chart-area"></i> Live Data
    </span>
  </div>
  <div id="custDailyChart" style="min-height:230px;"></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const raw    = @json($customerDailyChart);
    const days   = raw.map(r => r.day);
    const amounts = raw.map(r => parseFloat(r.amount));

    const isDark     = document.documentElement.getAttribute('data-theme') === 'dark';
    const labelColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor  = isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.04)';

    const options = {
      series: [{ name: 'Amount (৳)', data: amounts }],
      chart: {
        type: 'area',
        height: 230,
        toolbar: { show: false },
        zoom: { enabled: false },
        fontFamily: "'Inter', sans-serif",
        animations: { enabled: true, easing: 'easeinout', speed: 750 },
        background: 'transparent',
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2.5, colors: ['#4facfe'] },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          colorStops: [
            { offset: 0,   color: '#4facfe', opacity: 0.35 },
            { offset: 100, color: '#00f2fe', opacity: 0.02 },
          ],
        },
      },
      markers: {
        size: 0,
        hover: { size: 5, colors: ['#4facfe'], strokeColors: '#fff', strokeWidth: 2 },
      },
      xaxis: {
        categories: days,
        title: {
          text: 'Day of Month',
          style: { color: labelColor, fontSize: '11px', fontFamily: "'Inter', sans-serif" }
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: labelColor, fontSize: '11px', fontFamily: "'Inter', sans-serif" } },
      },
      yaxis: {
        labels: {
          formatter: v => '৳' + v.toLocaleString(),
          style: { colors: labelColor, fontSize: '11px', fontFamily: "'Inter', sans-serif" },
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

    new ApexCharts(document.querySelector('#custDailyChart'), options).render();
  });
</script>
@endpush
@endsection