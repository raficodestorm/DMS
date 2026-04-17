@extends('layouts.adminlayout')

@section('content')
<style>
  .stock-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    padding: 10px 0;
  }

  .branch-card {
    background: var(--section-bg);
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
  }

  .branch-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary);
    box-shadow: 0 10px 20px var(--glass);
  }

  .branch-card.company-card {
    background: linear-gradient(135deg, var(--primary), var(--primary-soft));
    border: none;
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

  <div class="stock-grid">
    <div class="branch-card company-card animate__animated animate__fadeIn"
      onclick="location.href='{{ route('admin.stocks.specific') }}'">
      <div>
        <i class="fas fa-building-columns"></i>
        <h2>Company Stock</h2>
        <p>Consolidated data from all branches</p>
      </div>
      <div class="total-stock text-end">
        <div class="stock-value">Stock Amount: <span style="color: var(--primary);">{{
            number_format($company_total_value, 2) }} TK</span></div>
      </div>
    </div>

    @foreach($branches as $branch)
    <div class="branch-card animate__animated animate__fadeIn"
      onclick="location.href='{{ route('admin.stocks.specific', $branch->id) }}'">
      <div>
        <i class="fas fa-store"></i>
        <h2>{{ $branch->name }}</h2>
        <p>{{ $branch->address ?? 'Main Branch' }}</p>
      </div>
      <div class="total-stock text-end">
        <div class="stock-value">Stock Amount: <span style="color: var(--primary);">{{
            number_format($branch->total_value, 2)}} TK</span></div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection