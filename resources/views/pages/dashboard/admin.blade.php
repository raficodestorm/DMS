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
@endsection


