@extends('layouts.customerlayout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 style="font-size: 22px; font-family: 'Cinzel', serif;">Dashboard Overview</h2>
  <span class="badge p-2 text-uppercase" style="color: var(--primary); background: var(--primary-soft);">
    {{ Auth::user()->role ?? '' }}
  </span>
  <span class="badge p-2" style="letter-spacing: 1px; color: var(--text-muted);">
    <?php echo date('d M, Y'); ?>
  </span>
</div>

<div class="dashboard-row row g-3">

  <div class="col-lg-3 col-6">
    <div class="stat-card">
      <div class="card-top">
        <div class="value-box">
          <h3><i class="mdi mdi-account-tie text-white"></i></h3>
          <p class="amount text-info">10</p>
        </div>
        <div class="trend-icon bg-info-soft"><span class="mdi mdi-account-check"></span></div>
      </div>
      <h6 class="card-label">Total Editors</h6>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="stat-card">
      <div class="card-top">
        <div class="value-box">
          <h3><i class="mdi mdi-shape text-white"></i></h3>
          <p class="amount text-warning">15</p>
        </div>
        <div class="trend-icon bg-warning-soft"><span class="mdi mdi-label-outline"></span></div>
      </div>
      <h6 class="card-label">Total Categories</h6>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="stat-card">
      <div class="card-top">
        <div class="value-box">
          <h3><i class="mdi mdi-newspaper-variant-outline text-white"></i></h3>
          <p class="amount text-success">20</p>
        </div>
        <div class="trend-icon bg-success-soft"><span class="mdi mdi-database-check"></span></div>
      </div>
      <h6 class="card-label">Total Posts</h6>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="stat-card today-highlight">
      <div class="card-top">
        <div class="value-box">
          <h3><i class="mdi mdi-calendar-star text-white"></i></h3>
          <p class="amount text-white">25</p>
        </div>
        <div class="trend-icon pulse-animation"><span class="mdi mdi-fire"></span></div>
      </div>
      <h6 class="card-label">Today's Posts</h6>
    </div>
  </div>

</div>


@endsection


@if(session('success'))
@push('scripts')
<script>
  Swal.fire({
    html: `
        <div class="success-wrapper">
            <div class="success-circle">
                <div class="checkmark"></div>
            </div>
            <h2 class="success-title">Success</h2>
            <p class="success-text">{{ session('success') }}</p>
        </div>
    `,
    showConfirmButton: false,
    timer: 2200,
    background: 'transparent',
    backdrop: 'rgba(0,0,0,0.3)',
    customClass: {
        popup: 'success-popup'
    }
});
</script>
@endpush
@endif