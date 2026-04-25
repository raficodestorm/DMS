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

  .branch-card.company-card {
    background: var(--primary);
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    backdrop-filter: blur(15px) saturate(180%);
    -webkit-backdrop-filter: blur(15px) saturate(180%);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3),
      0 5px 15px var(--glow-primary);
    overflow: hidden;
    position: relative;

  }

  .branch-card.company-card h2,
  .branch-card.company-card p,
  .branch-card.company-card i {
    color: #ffffff;
  }

  .branch-card i {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 10px;
    opacity: 0.8;
  }

  .branch-card h2 {
    font-size: 1.5rem;
    color: var(--text-main);
    margin-bottom: 5px;
  }

  .branch-card p {
    color: var(--text-muted);
    font-size: 0.9rem;
  }

  .stock-value {
    font-size: 1.2rem;
    margin-top: 5px;
    color: var(--text-muted);
  }

  .company-card .stock-value {
    color: #fff;
  }
</style>

<div class="container-fluid">
  <div class="card-header mb-4">
    <h2 style="color: var(--text-main);">Inventory Overview</h2>
    <p style="color: var(--text-muted);">Monitor stock valuation across all branches</p>
  </div>

  <div class="row g-4">

    <div class="col-12 col-sm-6 col-md-4">
      <div class="branch-card company-card animate__animated animate__fadeIn"
        onclick="location.href='{{ route('admin.stocks.specific') }}'">

        <div>
          <i class="fas fa-building-columns"></i>
          <h2>Company Stock</h2>
          <p>Consolidated data from all branches</p>
        </div>

        <div class="total-stock text-end">
          <div class="stock-value">
            Stock: {{ number_format($company_total_value, 2) }} TK
          </div>
        </div>

      </div>
    </div>

    @foreach($branches as $branch)

    <div class="col-12 col-sm-6 col-md-4">
      <div class="branch-card animate__animated animate__fadeIn"
        onclick="location.href='{{ route('admin.stocks.specific', $branch->id) }}'">

        <div>
          <i class="fas fa-store"></i>
          <h2>{{ $branch->name }}</h2>
          <p>{{ $branch->address ?? 'Main Branch' }}</p>
        </div>

        <div class="total-stock text-end">
          <div class="stock-value">
            Stock:
            <span style="color: var(--primary);">
              {{ number_format($branch->total_value, 2) }} TK
            </span>
          </div>
        </div>

      </div>
    </div>

    @endforeach

  </div>
</div>
@endsection