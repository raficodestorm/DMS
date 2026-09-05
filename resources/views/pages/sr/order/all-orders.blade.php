@extends('layouts.srlayout')

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
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
      <h2 style="color: var(--text-main); margin-bottom: 2px;">Customer Orders</h2>
      <p style="color: var(--text-muted); margin-bottom: 0;">Select a customer to view order history</p>
    </div>
    <div style="background: rgba(49,49,255,0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49,49,255,0.2);">
      <i class="fas fa-users me-1"></i> Showing: <span id="visibleCount">{{ count($customers) }}</span> / {{ count($customers) }}
    </div>
  </div>

  {{-- Live Search Bar --}}
  <div class="smart-filter-wrapper">
    <div style="display: flex; gap: 10px; align-items: flex-end;">
      <div style="flex: 1;">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search Customer</label>
        <div style="position: relative;">
          <input type="text" id="liveSearch" class="input-form" placeholder="Search by shop name or address..." style="padding-left: 32px; margin-bottom: 0;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
      </div>
      <div>
        <button type="button" id="resetSearchBtn" class="btn btn-outline-secondary" title="Reset Search" style="height: 36px; width: 42px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
          <i class="fas fa-undo"></i>
        </button>
      </div>
    </div>
  </div>

  <div class="row g-4" id="customerGrid">
    @foreach($customers as $customer)
    <div class="col-12 col-sm-6 col-md-4 animate__animated animate__fadeIn customer-col" data-search="{{ strtolower($customer->shop_name) }} {{ strtolower($customer->address ?? '') }}">
      <div class="branch-card" onclick="location.href='{{ route('sr.order.specific', $customer->id) }}'">

        <div class="customer-meta">
          <i class="fa-solid fa-user-tie"></i>
          <div>
            <h2>{{ $customer->shop_name }}</h2>
            <p class="mb-0" style="font-size: 0.8rem; color: var(--text-muted);">
              {{ Str::limit($customer->address, 30) }}
            </p>
          </div>
        </div>

        <div class="total-info">
          <div class="info-row">
            <span class="label">Total Orders:</span>
            <span class="value">{{ number_format($customer->total_order_amount, 2) }} TK</span>
          </div>
          <div class="info-row">
            <span class="label">Total Due:</span>
            <span class="value {{ $customer->due > 0 ? 'text-danger' : '' }}">
              {{ number_format($customer->due, 2) }} TK
            </span>
          </div>
        </div>

      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const liveSearch   = document.getElementById('liveSearch');
    const resetBtn     = document.getElementById('resetSearchBtn');
    const visibleCount = document.getElementById('visibleCount');
    const total        = document.querySelectorAll('.customer-col').length;

    function filterCards(query) {
        const q = query.toLowerCase().trim();
        let count = 0;

        document.querySelectorAll('.customer-col').forEach(col => {
            const text = col.getAttribute('data-search') || '';
            const match = !q || text.includes(q);
            col.style.display = match ? '' : 'none';
            if (match) count++;
        });

        if (visibleCount) visibleCount.innerText = q ? count : total;
    }

    if (liveSearch) {
        liveSearch.addEventListener('input', function () {
            filterCards(this.value);
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (liveSearch) liveSearch.value = '';
            filterCards('');
        });
    }
});
</script>
@endpush