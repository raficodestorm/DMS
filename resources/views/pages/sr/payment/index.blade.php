@extends('layouts.srlayout')

@section('content')
<div class="manage-card">

  <div class="card-header">
    <h2>All Transactions</h2>
    <p>View all payment and purchase records of your customers</p>
    @include('components.alert')
  </div>

  {{-- Smart Filter Bar --}}
  <div style="margin: 15px 0; background: var(--section-bg, #fff); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color, #e2e8f0);">
    <div class="row g-2 align-items-end">
      {{-- Search Bar --}}
      <div class="col-12 col-lg-5 order-1 order-lg-1">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Search</label>
        <div style="position: relative;">
          <input type="text" id="search" class="input-form" placeholder="Search by Transaction ID or Customer name..." value="{{ request('search') }}" style="margin-bottom: 0; padding-left: 35px; height: 42px;">
          <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>
      </div>

      {{-- From Date --}}
      <div class="col-6 col-lg-2 order-2 order-lg-3">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">From Date</label>
        <input type="date" id="fromDate" class="input-form" value="{{ request('from_date') }}" style="margin-bottom: 0; height: 42px;">
      </div>

      {{-- To Date --}}
      <div class="col-6 col-lg-2 order-3 order-lg-4">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">To Date</label>
        <input type="date" id="toDate" class="input-form" value="{{ request('to_date') }}" style="margin-bottom: 0; height: 42px;">
      </div>

      {{-- Type Filter --}}
      <div class="col-8 col-lg-2 order-4 order-lg-2">
        <label style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; display: block;">Type</label>
        <select id="typeFilter" class="input-form" style="margin-bottom: 0; height: 42px; padding: 5px;">
          <option value="">-- All Types --</option>
          <option value="pay" {{ request('type') == 'pay' ? 'selected' : '' }}>Payment</option>
          <option value="buy" {{ request('type') == 'buy' ? 'selected' : '' }}>Purchase</option>
          <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
        </select>
      </div>

      {{-- Reset Button --}}
      <div class="col-4 col-lg-1 order-5 order-lg-5">
        <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100" title="Reset Filters" style="margin-bottom: 0; height: 42px; display: inline-flex; align-items: center; justify-content: center;">
          <i class="fas fa-undo"></i>
        </button>
      </div>
    </div>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Transaction ID</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date & Time</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody class="desktop-table" id="desktopTable">
        @include('pages.sr.payment.table')
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    @include('pages.sr.payment.mtable')
  </div>

</div>

<div class="mt-3">
  {{ $payments->links() }}
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const typeFilter  = document.getElementById('typeFilter');
    const fromDate    = document.getElementById('fromDate');
    const toDate      = document.getElementById('toDate');
    const resetBtn    = document.getElementById('resetBtn');

    function fetchFilteredPayments() {
        const query = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
        const type  = encodeURIComponent(typeFilter ? typeFilter.value : '');
        const from  = encodeURIComponent(fromDate ? fromDate.value : '');
        const to    = encodeURIComponent(toDate ? toDate.value : '');

        const url = `{{ route('sr.payments.index') }}?search=${query}&type=${type}&from_date=${from}&to_date=${to}`;

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('desktopTable').innerHTML = data.table;
            document.getElementById('mobileTable').innerHTML = data.mobile;
        })
        .catch(err => console.error('Filter fetch error:', err));
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchFilteredPayments, 350);
        });
    }

    if (typeFilter) {
        typeFilter.addEventListener('change', fetchFilteredPayments);
    }

    if (fromDate) {
        fromDate.addEventListener('change', fetchFilteredPayments);
    }

    if (toDate) {
        toDate.addEventListener('change', fetchFilteredPayments);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (typeFilter) typeFilter.value = '';
            if (fromDate) fromDate.value = '';
            if (toDate) toDate.value = '';
            fetchFilteredPayments();
        });
    }
});
</script>
@endpush