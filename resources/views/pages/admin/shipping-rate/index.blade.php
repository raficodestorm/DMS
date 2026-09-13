@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  {{-- Card Header --}}
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Shipping Rates</h2>
      <p class="text-muted mb-0">Manage location-based shipping charges and delivery fees</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(16, 185, 129, 0.25);">
        <i class="fas fa-truck-fast me-1"></i> Active Rates: <span id="activeRatesCount">{{ $activeRates ?? 0 }}</span>
      </div>
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-layer-group me-1"></i> Total: <span id="totalRatesCount">{{ $totalRates ?? 0 }}</span>
      </div>
      <a href="{{ route('admin.shipping-rates.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> Add Shipping Rate
      </a>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-5">

      {{-- Search --}}
      <div>
        <label>Search</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="Search rate name, city or country..." value="{{ request('search') }}" style="padding-left: 30px;">
          <i class="fas fa-search" style="position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.75rem;"></i>
        </div>
      </div>

      {{-- Country Filter --}}
      <div>
        <label>Country</label>
        <select id="countryFilter" class="input-form">
          <option value="">-- All Countries --</option>
          @foreach($countries as $c)
          <option value="{{ $c }}" {{ request('country') == $c ? 'selected' : '' }}>{{ $c }}</option>
          @endforeach
        </select>
      </div>

      {{-- City Filter --}}
      <div>
        <label>City</label>
        <select id="cityFilter" class="input-form">
          <option value="">-- All Cities --</option>
          @foreach($cities as $ct)
          <option value="{{ $ct }}" {{ request('city') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
          @endforeach
        </select>
      </div>

      {{-- Status Filter --}}
      <div>
        <label>Status</label>
        <select id="statusFilter" class="input-form">
          <option value="">-- All Status --</option>
          <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
          <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>

      {{-- Reset Button --}}
      <div>
        <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters & Show All" style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
          <i class="fas fa-undo"></i>
        </button>
      </div>

    </div>
  </div>

  {{-- Desktop Table --}}
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Rate Name</th>
          <th>Country</th>
          <th>City / Area</th>
          <th>Base Rate</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="desktopTable">
        <tr>
          <td colspan="7" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view shipping rates.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards" id="mobileTable">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view shipping rates.
    </p>
  </div>

</div>

{{-- Pagination --}}
<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
window.toggleShippingRateStatus = function(btn) {
    const url = btn.dataset.url;
    if (!url) return;

    btn.disabled = true;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(res => {
        if (!res.ok) throw new Error('Network error');
        return res.json();
    })
    .then(data => {
        if (data.success) {
            if (data.status) {
                btn.innerHTML = '<span class="status-active-badge">● Active</span>';
            } else {
                btn.innerHTML = '<span class="status-inactive-badge">● Inactive</span>';
            }

            // Refresh data / counters
            if (typeof fetchFilteredShippingRates === 'function') {
                fetchFilteredShippingRates();
            }
        } else {
            alert(data.message || 'Failed to update status.');
        }
    })
    .catch(err => {
        console.error('Toggle status error:', err);
        alert('Failed to update status. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
    });
};

document.addEventListener('DOMContentLoaded', function () {
    const searchInput        = document.getElementById('searchInput');
    const countryFilter      = document.getElementById('countryFilter');
    const cityFilter         = document.getElementById('cityFilter');
    const statusFilter       = document.getElementById('statusFilter');
    const resetBtn            = document.getElementById('resetBtn');

    const desktopTable       = document.getElementById('desktopTable');
    const mobileTable        = document.getElementById('mobileTable');
    const activeRatesCount   = document.getElementById('activeRatesCount');
    const totalRatesCount    = document.getElementById('totalRatesCount');
    const paginationWrapper  = document.getElementById('paginationWrapper');

    const allCities = @json($cities ?? []);
    const countriesData = @json($countriesData ?? []);

    function updateCityDropdown(country) {
        if (!cityFilter) return;
        const currentSelectedCity = cityFilter.value;
        cityFilter.innerHTML = '<option value="">-- All Cities --</option>';

        let citiesToShow = [];
        if (country && countriesData[country]) {
            citiesToShow = countriesData[country];
        } else {
            citiesToShow = allCities;
        }

        citiesToShow.forEach(city => {
            const opt = document.createElement('option');
            opt.value = city;
            opt.textContent = city;
            if (city === currentSelectedCity) {
                opt.selected = true;
            }
            cityFilter.appendChild(opt);
        });
    }

    function showLoadingState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading shipping rates...
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading shipping rates...
                </p>`;
        }
    }

    function showErrorState() {
        if (desktopTable) {
            desktopTable.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load shipping rates. Please try again.
                    </td>
                </tr>`;
        }
        if (mobileTable) {
            mobileTable.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load shipping rates.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput)   searchInput.value   = '';
        if (countryFilter) countryFilter.value = '';
        if (cityFilter)    cityFilter.value    = '';
        if (statusFilter)  statusFilter.value  = '';
        updateCityDropdown('');
    }

    function fetchFilteredShippingRates(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search  = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            const country = encodeURIComponent(countryFilter ? countryFilter.value : '');
            const city    = encodeURIComponent(cityFilter ? cityFilter.value : '');
            const status  = encodeURIComponent(statusFilter ? statusFilter.value : '');

            url = `{{ route('admin.shipping-rates.index.data') }}?search=${search}&country=${country}&city=${city}&status=${status}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (desktopTable) desktopTable.innerHTML = data.table;
            if (mobileTable)  mobileTable.innerHTML  = data.mobile;
            if (activeRatesCount && data.activeRates !== undefined) {
                activeRatesCount.innerText = data.activeRates;
            }
            if (totalRatesCount && data.totalRates !== undefined) {
                totalRatesCount.innerText = data.totalRates;
            }
            if (paginationWrapper && data.pagination !== undefined) {
                paginationWrapper.innerHTML = data.pagination;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showErrorState();
        });
    }

    // Expose globally for toggle status
    window.fetchFilteredShippingRates = fetchFilteredShippingRates;

    // Initial Load: Only fetch if filters or page parameter exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredShippingRates();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredShippingRates(), 400);
        });
    }

    if (countryFilter) {
        countryFilter.addEventListener('change', function () {
            updateCityDropdown(this.value);
            fetchFilteredShippingRates();
        });
    }

    if (cityFilter)   cityFilter.addEventListener('change',   () => fetchFilteredShippingRates());
    if (statusFilter) statusFilter.addEventListener('change', () => fetchFilteredShippingRates());

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredShippingRates();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredShippingRates(link.href);
            }
        });
    }
});
</script>
@endpush
