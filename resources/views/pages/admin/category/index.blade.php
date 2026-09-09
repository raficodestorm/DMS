@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0">All Categories</h2>
            <p class="text-muted mb-0">Manage and organize product categories</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
                <i class="fas fa-tags me-1"></i> Total Categories: <span id="totalCategoryCount">{{ $totalCategories ?? 0 }}</span>
            </div>
            <div style="background: rgba(245, 158, 11, 0.1); color: #d97706; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(245, 158, 11, 0.28);">
                <i class="fas fa-star me-1" style="color: #f59e0b;"></i> Featured: <span id="totalFeaturedCount">{{ $totalFeatured ?? 0 }}</span>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn-smart btn-blue">
                <i class="fas fa-plus me-1"></i> Add New Category
            </a>
        </div>
    </div>

    @include('components.alert')

    {{-- Smart Filter Bar --}}
    <div class="smart-filter-wrapper">
        <div class="smart-filter-grid-2">

            {{-- Search --}}
            <div>
                <label>Search</label>
                <div style="position: relative;">
                    <input type="text" id="searchInput" class="input-form" placeholder="Search by Category Name or Description..." value="{{ request('search') }}" style="padding-left: 32px;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                </div>
            </div>

            {{-- Reset Button --}}
            <div>
                <button type="button" id="resetBtn" class="btn btn-outline-secondary" title="Reset Filters & Show All" style="height: 36px; width: 100%; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
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
                    <th>Image</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Products</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="categoryTable">
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view categories.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="manage-mobile-cards" id="categoryMobile">
        <p class="text-center text-muted py-5">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view categories.
        </p>
    </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
window.toggleCategoryFeatured = function(btn) {
    const url = btn.dataset.url;
    if (!url) return;

    const icon = btn.querySelector('i');
    const origIconClass = icon ? icon.className : '';
    if (icon) icon.className = 'fa-solid fa-spinner fa-spin';
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
            if (data.is_featured) {
                btn.classList.add('is-featured');
                if (icon) icon.className = 'fa-solid fa-star';
                btn.title = 'Featured (Click to unfeature)';
            } else {
                btn.classList.remove('is-featured');
                if (icon) icon.className = 'fa-regular fa-star';
                btn.title = 'Mark as Featured';
            }
            if (data.total_featured !== undefined) {
                const totalFeaturedEl = document.getElementById('totalFeaturedCount');
                if (totalFeaturedEl) totalFeaturedEl.innerText = data.total_featured;
            }
        } else {
            if (icon) icon.className = origIconClass;
            alert(data.message || 'Failed to update featured status.');
        }
    })
    .catch(err => {
        console.error('Toggle featured error:', err);
        if (icon) icon.className = origIconClass;
        alert('Failed to update featured status. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
    });
};

document.addEventListener('DOMContentLoaded', function () {
    const searchInput       = document.getElementById('searchInput');
    const resetBtn          = document.getElementById('resetBtn');

    const categoryTable     = document.getElementById('categoryTable');
    const categoryMobile    = document.getElementById('categoryMobile');
    const totalCountEl      = document.getElementById('totalCategoryCount');
    const totalFeaturedEl   = document.getElementById('totalFeaturedCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (categoryTable) {
            categoryTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading categories...
                    </td>
                </tr>`;
        }
        if (categoryMobile) {
            categoryMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading categories...
                </p>`;
        }
    }

    function showErrorState() {
        if (categoryTable) {
            categoryTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load category data. Please try again.
                    </td>
                </tr>`;
        }
        if (categoryMobile) {
            categoryMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load category data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput) searchInput.value = '';
    }

    function fetchFilteredCategories(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const search = encodeURIComponent(searchInput ? searchInput.value.trim() : '');
            url = `{{ route('admin.categories.index.data') }}?search=${search}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (categoryTable)  categoryTable.innerHTML  = data.table;
            if (categoryMobile) categoryMobile.innerHTML = data.mobile;
            if (totalCountEl && data.total !== undefined) {
                totalCountEl.innerText = data.total;
            }
            if (totalFeaturedEl && data.total_featured !== undefined) {
                totalFeaturedEl.innerText = data.total_featured;
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

    // Initial Load: Only fetch if filters or page parameter exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString().length > 0) {
        fetchFilteredCategories();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredCategories(), 450);
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredCategories();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredCategories(link.href);
            }
        });
    }
});
</script>
@endpush