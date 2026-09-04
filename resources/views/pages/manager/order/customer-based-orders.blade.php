@extends('layouts.managerlayout')

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
  <div class="card-header mb-3">
    <h2 style="color: var(--text-main);">Customer Orders</h2>
    <p style="color: var(--text-muted);">Select a customer to view order history</p>
  </div>

  {{-- Search Bar --}}
  <div class="smart-filter-wrapper mb-4" style="background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-9">
        <div style="position: relative;">
          <input type="text" id="customerSearchInput" class="input-form" placeholder="Search Customer by shop name or address..." style="padding-left: 35px; height: 42px; margin-bottom: 0;">
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
      </div>
      <div class="col-12 col-md-3">
        <button type="button" id="customerResetBtn" class="btn btn-outline-secondary w-100" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
          <i class="fas fa-undo"></i> Reset
        </button>
      </div>
    </div>
  </div>

  <div class="row g-4">
    @foreach($customers as $customer)
    <div class="col-12 col-sm-6 col-md-4 animate__animated animate__fadeIn">
      <div class="branch-card" onclick="location.href='{{ route('manager.order.specific.customer', $customer->id) }}'">

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('customerSearchInput');
    const resetBtn    = document.getElementById('customerResetBtn');

    function filterCards() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const cards = document.querySelectorAll('.branch-card');
        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            const col  = card.closest('.col-12');
            if (col) {
                col.style.display = text.includes(query) ? '' : 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', filterCards);
        searchInput.addEventListener('input', filterCards);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            filterCards();
        });
    }
});
</script>
@endpush
@endsection