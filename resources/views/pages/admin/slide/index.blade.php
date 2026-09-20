@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

  {{-- Card Header --}}
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h2 class="mb-0">Slide Control</h2>
      <p class="text-muted mb-0">Manage homepage hero banners, display positions & linked offers</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <div style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(16, 185, 129, 0.25);">
        <i class="fas fa-eye me-1"></i> Active Slides: <span id="activeSlidesCount">{{ $activeSlides ?? 0 }}</span>
      </div>
      <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
        <i class="fas fa-layer-group me-1"></i> Total: <span id="totalSlidesCount">{{ $totalSlides ?? 0 }}</span>
      </div>
      <a href="{{ route('admin.slides.create') }}" class="btn-smart btn-blue">
        <i class="fas fa-plus me-1"></i> Add New Slide
      </a>
    </div>
  </div>

  @include('components.alert')

  {{-- Smart Filter Bar --}}
  <div class="smart-filter-wrapper">
    <div class="smart-filter-grid-4">

      {{-- Search --}}
      <div>
        <label>Search</label>
        <div style="position: relative;">
          <input type="text" id="searchInput" class="input-form" placeholder="Search slide name or offer..." value="{{ request('search') }}" style="padding-left: 32px;">
          <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
        </div>
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

      {{-- Offer Filter --}}
      <div>
        <label>Connected Offer</label>
        <select id="offerFilter" class="input-form">
          <option value="">-- All Offers --</option>
          <option value="none">Without Specific Offer</option>
          @foreach($retailOffers as $off)
            <option value="{{ $off->id }}">{{ $off->name }} @if($off->product)({{ $off->product->name }})@endif</option>
          @endforeach
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
          <th>Banner Preview</th>
          <th>Slide Name</th>
          <th>Position</th>
          <th>Connected Offer / Link</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody class="desktop-table" id="slideTable">
        <tr>
          <td colspan="7" class="text-center py-5 text-muted">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view slide banners.
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile Cards --}}
  <div class="manage-mobile-cards d-md-none" id="slideMobile">
    <p class="text-center text-muted py-5">
      <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view slide banners.
    </p>
  </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.getElementById('searchInput');
    const statusFilter      = document.getElementById('statusFilter');
    const offerFilter       = document.getElementById('offerFilter');
    const resetBtn          = document.getElementById('resetBtn');
    const tableBody         = document.getElementById('slideTable');
    const mobileContainer   = document.getElementById('slideMobile');
    const paginationWrapper = document.getElementById('paginationWrapper');
    const activeCountSpan   = document.getElementById('activeSlidesCount');
    const totalCountSpan    = document.getElementById('totalSlidesCount');

    let debounceTimer;

    function showLoadingState() {
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading slides...
                    </td>
                </tr>`;
        }
        if (mobileContainer) {
            mobileContainer.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading slides...
                </p>`;
        }
    }

    function fetchSlides(page = 1, customUrl = null) {
        showLoadingState();

        let url = customUrl;
        if (!url) {
            const params = new URLSearchParams({
                page: page,
                search: searchInput ? searchInput.value.trim() : '',
                status: statusFilter ? statusFilter.value : '',
                offer_id: offerFilter ? offerFilter.value : ''
            });
            url = `{{ route('admin.slides.index.data') }}?${params.toString()}`;
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (tableBody) tableBody.innerHTML = data.table;
            if (mobileContainer) mobileContainer.innerHTML = data.mobile;
            if (paginationWrapper && data.pagination !== undefined) paginationWrapper.innerHTML = data.pagination;
            if (activeCountSpan && data.activeSlides !== undefined) activeCountSpan.innerText = data.activeSlides;
            if (totalCountSpan && data.totalSlides !== undefined) totalCountSpan.innerText = data.totalSlides;
        })
        .catch(err => {
            console.error('Error fetching slides:', err);
            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-circle me-1"></i> Failed to load slide data. Please try again.
                        </td>
                    </tr>`;
            }
        });
    }

    // Initial Load: Only fetch if filters or page parameter exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchSlides();
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchSlides(1), 350);
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', () => fetchSlides(1));
    }

    if (offerFilter) {
        offerFilter.addEventListener('change', () => fetchSlides(1));
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (offerFilter) offerFilter.value = '';
            fetchSlides(1);
        });
    }

    // Pagination Click delegation
    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a.page-link') || e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchSlides(null, link.href);
            }
        });
    }

    // Global Status Toggle function
    window.toggleSlideStatus = function(slideId, btnElement) {
        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btnElement.disabled = true;

        fetch(`{{ url('admin/slides') }}/${slideId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.status) {
                    btnElement.style.background = 'rgba(16, 185, 129, 0.15)';
                    btnElement.style.color = '#059669';
                    btnElement.innerHTML = '<i class="fas fa-check-circle me-1"></i><span>Active</span>';
                } else {
                    btnElement.style.background = 'rgba(239, 68, 68, 0.15)';
                    btnElement.style.color = '#dc2626';
                    btnElement.innerHTML = '<i class="fas fa-times-circle me-1"></i><span>Inactive</span>';
                }
                if (activeCountSpan) activeCountSpan.innerText = data.totalActive;
            }
        })
        .catch(err => {
            console.error('Toggle status error:', err);
            btnElement.innerHTML = originalHtml;
        })
        .finally(() => {
            btnElement.disabled = false;
        });
    };
});
</script>
@endpush
