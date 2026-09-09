@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-0">All Products</h2>
            <p class="text-muted mb-0">Manage all registered products</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div style="background: rgba(49, 49, 255, 0.08); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(49, 49, 255, 0.2);">
                <i class="fas fa-box me-1"></i> Total Products: <span id="totalProductCount">{{ $totalProducts ?? 0 }}</span>
            </div>
            <div style="background: rgba(245, 158, 11, 0.1); color: #d97706; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid rgba(245, 158, 11, 0.28);">
                <i class="fas fa-star me-1" style="color: #f59e0b;"></i> Featured: <span id="totalFeaturedCount">{{ $totalFeatured ?? 0 }}</span>
            </div>
            <a href="{{ route('admin.products.export.excel') }}"
               id="excelDownloadBtn"
               class="btn-smart"
               style="background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; border: none; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: opacity .2s, transform .2s;"
               title="Download all products as Excel"
               onclick="handleExcelDownload(event)">
                <i class="fas fa-file-excel" id="excelBtnIcon"></i>
                <span id="excelBtnText">Download Excel</span>
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn-smart btn-blue">
                <i class="fas fa-plus me-1"></i> Add New Product
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
                    <input type="text" id="searchInput" class="input-form" placeholder="Search by Name, SKU, Barcode..." value="{{ request('search') }}" style="padding-left: 32px;">
                    <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                </div>
            </div>

            {{-- Supplier Filter --}}
            <div>
                <label>Supplier / Brand</label>
                <select id="supplierFilter" class="input-form">
                    <option value="">-- All Suppliers --</option>
                    @foreach($suppliers ?? [] as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->company_name ?? $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Featured Filter --}}
            <div>
                <label>Featured</label>
                <select id="featuredFilter" class="input-form">
                    <option value="">-- All Products --</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>⭐ Featured Only</option>
                    <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Non-Featured</option>
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

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table" id="productTable">
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view products.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="manage-mobile-cards" id="productMobile">
        <p class="text-center text-muted py-5">
            <i class="fas fa-filter me-1" style="color: var(--primary);"></i> Select filters or click the reset button to view products.
        </p>
    </div>

</div>

<div class="mt-3" id="paginationWrapper"></div>

@endsection

@push('scripts')
<script>
window.toggleFeatured = function(btn) {
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
    const supplierFilter    = document.getElementById('supplierFilter');
    const featuredFilter    = document.getElementById('featuredFilter');
    const resetBtn          = document.getElementById('resetBtn');

    const productTable      = document.getElementById('productTable');
    const productMobile     = document.getElementById('productMobile');
    const totalCountEl      = document.getElementById('totalProductCount');
    const totalFeaturedEl   = document.getElementById('totalFeaturedCount');
    const paginationWrapper = document.getElementById('paginationWrapper');

    function showLoadingState() {
        if (productTable) {
            productTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading products...
                    </td>
                </tr>`;
        }
        if (productMobile) {
            productMobile.innerHTML = `
                <p class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin me-2"></i> Loading products...
                </p>`;
        }
    }

    function showErrorState() {
        if (productTable) {
            productTable.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Failed to load product data. Please try again.
                    </td>
                </tr>`;
        }
        if (productMobile) {
            productMobile.innerHTML = `
                <p class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-circle me-1"></i> Failed to load product data.
                </p>`;
        }
    }

    function clearAllFilterInputs() {
        if (searchInput) searchInput.value = '';
        if (supplierFilter) supplierFilter.value = '';
        if (featuredFilter) featuredFilter.value = '';
    }

    function fetchFilteredProducts(fetchUrl = null) {
        showLoadingState();

        let url = fetchUrl;
        if (!url) {
            const params = new URLSearchParams();
            const search = searchInput ? searchInput.value.trim() : '';
            const supplierId = supplierFilter ? supplierFilter.value : '';
            const featured = featuredFilter ? featuredFilter.value : '';

            if (search) params.append('search', search);
            if (supplierId) params.append('supplier_id', supplierId);
            if (featured !== '') params.append('featured', featured);

            url = `{{ route('admin.products.index.data') }}?${params.toString()}`;
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(data => {
            if (productTable)  productTable.innerHTML  = data.table;
            if (productMobile) productMobile.innerHTML = data.mobile;
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

    // Initial Load: Check if filters or page parameter exist in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('supplier_id') && supplierFilter) {
        supplierFilter.value = urlParams.get('supplier_id');
    }
    if (urlParams.get('featured') !== null && featuredFilter) {
        featuredFilter.value = urlParams.get('featured');
    }
    if (urlParams.toString().length > 0) {
        fetchFilteredProducts();
    }

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchFilteredProducts(), 450);
        });
    }

    if (supplierFilter) {
        supplierFilter.addEventListener('change', function () {
            fetchFilteredProducts();
        });
    }

    if (featuredFilter) {
        featuredFilter.addEventListener('change', function () {
            fetchFilteredProducts();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            clearAllFilterInputs();
            fetchFilteredProducts();
        });
    }

    if (paginationWrapper) {
        paginationWrapper.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href) {
                e.preventDefault();
                fetchFilteredProducts(link.href);
            }
        });
    }
});

// ── Excel Download Handler ─────────────────────────────────────────────
window.handleExcelDownload = function(e) {
    const btn  = document.getElementById('excelDownloadBtn');
    const icon = document.getElementById('excelBtnIcon');
    const text = document.getElementById('excelBtnText');

    if (!btn || btn.classList.contains('downloading')) return;

    btn.classList.add('downloading');
    btn.style.opacity      = '0.75';
    btn.style.pointerEvents = 'none';
    if (icon) icon.className = 'fas fa-spinner fa-spin';
    if (text) text.textContent = 'Preparing...';

    // Restore after a generous timeout (file download starts in background)
    setTimeout(function () {
        btn.classList.remove('downloading');
        btn.style.opacity       = '1';
        btn.style.pointerEvents = 'auto';
        if (icon) icon.className = 'fas fa-file-excel';
        if (text) text.textContent = 'Download Excel';
    }, 4000);
};
</script>
@endpush