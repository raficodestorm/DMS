@extends('layouts.adminlayout')

@section('content')
<style>
  .branch-card {
    background: var(--glass);
    backdrop-filter: blur(15px) saturate(180%);
    -webkit-backdrop-filter: blur(15px) saturate(180%);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 25px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    /* Column-er height fill korar jonno */
    min-height: 200px;
  }

  .branch-card::before {
    content: "";
    position: absolute;
    top: -60px;
    right: -60px;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(241, 74, 241, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    filter: blur(20px);
    z-index: 0;
  }

  .branch-card::after {
    content: "";
    position: absolute;
    bottom: -60px;
    left: -60px;
    width: 180px;
    height: 180px;
    background: radial-gradient(circle, rgba(98, 140, 239, 0.352) 0%, transparent 70%);
    border-radius: 50%;
    filter: blur(25px);
    z-index: 1;
    pointer-events: none;

  }

  .branch-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary);
    box-shadow: 0 10px 20px var(--glass);
    background: rgba(255, 255, 255, 0.08);
  }

  .branch-card i {
    font-size: 3rem;
    color: var(--primary);
    margin-bottom: 12px;
    z-index: 1;
  }

  /* Title logic adjustment */
  .customer-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    z-index: 1;
  }

  .branch-card h2 {
    font-size: 1.2rem;
    color: var(--text-main);
    margin-bottom: 3px;
    font-weight: 600;
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 0.9rem;
    z-index: 1;
  }

  .label {
    color: var(--text-muted);
  }

  .value {
    color: var(--text-main);
    font-weight: 600;
  }
</style>

<div class="container-fluid">
  <div class="card-header mb-4">
    <h2 style="color: var(--text-main);">Branch Orders</h2>
    <p style="color: var(--text-muted);">Select a branch to view order history</p>
  </div>

  <div class="row g-4">
    @foreach($branches as $branch)
    <div class="col-12 col-sm-6 col-md-4 animate__animated animate__fadeIn">
      <div class="branch-card" onclick="location.href='{{ route('admin.order.specific.branch', $branch->id) }}'">

        <div class="customer-meta">
          <i class="fa-solid fa-user-tie"></i>
          <div>
            <h2>{{ $branch->name }}</h2>
            <p class="mb-0" style="font-size: 0.8rem; color: var(--text-muted);">
              {{ Str::limit($branch->manager) }}
            </p>
          </div>
        </div>

        <div class="total-info">
          <div class="info-row">
            <span class="label">Total Orders:</span>
            <span class="value">{{ number_format($branch->total_orders) }}</span>
          </div>
          <div class="info-row">
            <span class="label">Total Sales:</span>
            <span class="value">
              {{ number_format($branch->total_order_amount, 2) }} TK
            </span>
          </div>
        </div>

      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection