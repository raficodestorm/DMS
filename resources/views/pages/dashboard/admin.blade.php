@extends('layouts.adminlayout')

@section('content')
<style>
  .dashboard-row {
    overflow-x: hidden; /* Prevent horizontal scroll from animations */
  }
  .stat-card {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    background: var(--section-bg);
    border-radius: 12px !important;
    border: none !important;
    padding: 20px !important;
  }
  .stat-card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transform: translateY(-5px);
  }
  .card-label {
    font-weight: 600 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 12px !important;
  }
  .amount {
    letter-spacing: -0.5px;
  }
  .trend-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px !important;
    transition: transform 0.3s ease;
  }
  .stat-card:hover .trend-icon {
    transform: scale(1.1);
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 style="font-size: 22px; font-family: 'Cinzel', serif; font-weight: 700; color: var(--primary);">Dashboard Overview</h2>
  <span class="badge p-2 px-3" style="letter-spacing: 1px; color: var(--text-muted); background: var(--section-bg); border-radius: 50px; border: 1px solid var(--border-color);">
    <i class="far fa-calendar-alt me-1"></i> {{ now()->format('d M, Y') }}
  </span>
</div>

<div class="dashboard-row row g-4">

  <!-- Total Company Stock -->
  <div class="col-xl-3 col-md-6">
    <div class="stat-card animate__animated animate__fadeInUp" style="border-top: 4px solid #4e73df !important;">
      <div class="card-top mb-0">
        <div class="value-box w-100 justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="trend-icon" style="background: rgba(78, 115, 223, 0.1); color: #4e73df; width: 48px; height: 48px; font-size: 22px;">
              <i class="fas fa-warehouse"></i>
            </div>
            <div>
              <h6 class="card-label mb-1">Stock Value</h6>
              <p class="amount mb-0" style="color: var(--text-main); font-size: 20px; font-weight: 700;">৳ {{ number_format($totalStockValue, 2) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Current Month Sales -->
  <div class="col-xl-3 col-md-6">
    <div class="stat-card animate__animated animate__fadeInUp" style="border-top: 4px solid #1cc88a !important; animation-delay: 0.1s;">
      <div class="card-top mb-0">
        <div class="value-box w-100 justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="trend-icon" style="background: rgba(28, 200, 138, 0.1); color: #1cc88a; width: 48px; height: 48px; font-size: 22px;">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
              <h6 class="card-label mb-1">{{ $monthName }} Sales</h6>
              <p class="amount mb-0" style="color: var(--text-main); font-size: 20px; font-weight: 700;">৳ {{ number_format($currentMonthSales, 2) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Current Month Profit -->
  <div class="col-xl-3 col-md-6">
    <div class="stat-card animate__animated animate__fadeInUp" style="border-top: 4px solid #36b9cc !important; animation-delay: 0.2s;">
      <div class="card-top mb-0">
        <div class="value-box w-100 justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="trend-icon" style="background: rgba(54, 185, 204, 0.1); color: #36b9cc; width: 48px; height: 48px; font-size: 22px;">
              <i class="fas fa-coins"></i>
            </div>
            <div>
              <h6 class="card-label mb-1">{{ $monthName }} Profit</h6>
              <p class="amount mb-0" style="color: var(--text-main); font-size: 20px; font-weight: 700;">৳ {{ number_format($currentMonthProfit, 2) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Current Month Cost -->
  <div class="col-xl-3 col-md-6">
    <div class="stat-card animate__animated animate__fadeInUp" style="border-top: 4px solid #f6c23e !important; animation-delay: 0.3s;">
      <div class="card-top mb-0">
        <div class="value-box w-100 justify-content-between">
          <div class="d-flex align-items-center gap-3">
            <div class="trend-icon" style="background: rgba(246, 194, 62, 0.1); color: #f6c23e; width: 48px; height: 48px; font-size: 22px;">
              <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
              <h6 class="card-label mb-1">{{ $monthName }} Cost</h6>
              <p class="amount mb-0" style="color: var(--text-main); font-size: 20px; font-weight: 700;">৳ {{ number_format($currentMonthCost, 2) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="row mt-4">
  <div class="col-12">
    <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s; background: var(--section-bg); border-left: 4px solid var(--primary) !important;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h5 class="card-label mb-1" style="font-size: 16px !important; color: var(--primary);">Yearly Sales Analysis</h5>
          <p class="text-muted mb-0" style="font-size: 12px;">Monthly sales performance for the year {{ now()->year }}</p>
        </div>
        <div class="chart-actions">
          <span class="badge bg-primary-soft text-primary p-2 px-3" style="background: rgba(78, 115, 223, 0.1); border-radius: 8px;">
            <i class="fas fa-chart-line me-1"></i> Real-time Data
          </span>
        </div>
      </div>
      <div id="yearlySalesChart" style="min-height: 350px;"></div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($yearlySalesChart);
    
    const options = {
      series: [{
        name: 'Total Sales',
        data: chartData.map(item => item.sales)
      }],
      chart: {
        height: 350,
        type: 'area',
        toolbar: {
          show: false
        },
        fontFamily: "'Inter', sans-serif",
        zoom: {
          enabled: false
        },
        animations: {
          enabled: true,
          easing: 'easeinout',
          speed: 800,
          animateOnLegendClick: true
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 3,
        colors: ['#4e73df']
      },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.45,
          opacityTo: 0.05,
          stops: [20, 100, 100],
          colorStops: [
            {
              offset: 0,
              color: '#4e73df',
              opacity: 0.4
            },
            {
              offset: 100,
              color: '#4e73df',
              opacity: 0.1
            }
          ]
        }
      },
      xaxis: {
        categories: chartData.map(item => item.month),
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: '#858796',
            fontSize: '12px',
            fontWeight: 500
          }
        }
      },
      yaxis: {
        labels: {
          formatter: function(value) {
            return "৳ " + value.toLocaleString();
          },
          style: {
            colors: '#858796',
            fontSize: '12px',
            fontWeight: 500
          }
        }
      },
      tooltip: {
        theme: 'dark',
        x: {
          show: true
        },
        y: {
          formatter: function(value) {
            return "৳ " + value.toLocaleString();
          }
        },
        marker: {
          show: true,
        }
      },
      grid: {
        borderColor: 'rgba(0,0,0,0.05)',
        strokeDashArray: 4,
        padding: {
          top: 0,
          right: 0,
          bottom: 0,
          left: 10
        }
      },
      markers: {
        size: 4,
        colors: ['#4e73df'],
        strokeColors: '#fff',
        strokeWidth: 2,
        hover: {
          size: 6
        }
      }
    };

    const chart = new ApexCharts(document.querySelector("#yearlySalesChart"), options);
    chart.render();
  });
</script>
@endpush
@endsection


