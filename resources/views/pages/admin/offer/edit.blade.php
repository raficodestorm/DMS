@extends('layouts.adminlayout')

@section('content')

<style>
    /* ── Product Live Search (PLS) ──────────────────────── */
    .dynamic-field-anim {
        animation: fieldFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes fieldFadeIn {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .pls-container {
        position: relative;
    }
    .pls-input-wrap {
        display: flex;
        align-items: center;
        background: var(--background, #f8fafc);
        border: 1.5px solid var(--border-color, #cbd5e1);
        border-radius: 10px;
        padding: 0 14px;
        transition: all 0.2s ease;
    }
    .pls-input-wrap:focus-within {
        border-color: var(--primary, #3131ff);
        box-shadow: 0 0 0 3px var(--primary-soft, rgba(49, 49, 255, 0.12));
        background: var(--section-bg, #ffffff);
    }
    .pls-search-icon {
        color: var(--text-muted, #64748b);
        margin-right: 10px;
        font-size: 14px;
        flex-shrink: 0;
    }
    .pls-input {
        flex: 1;
        min-width: 0;
        border: none;
        outline: none;
        background: transparent;
        padding: 10px 0;
        font-size: 14px;
        color: var(--text-main, #1e293b);
    }
    .pls-input::placeholder {
        color: var(--text-muted, #94a3b8);
        font-size: 13.5px;
    }
    .pls-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: var(--section-bg, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        max-height: 280px;
        overflow-y: auto;
        z-index: 99999;
        padding: 4px 0;
    }
    .pls-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color, #f1f5f9);
        transition: background 0.15s ease;
    }
    .pls-item:last-child {
        border-bottom: none;
    }
    .pls-item:hover,
    .pls-item.active {
        background: var(--primary-soft, #eef2ff);
    }
    .pls-item-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    .pls-thumb {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: var(--background, #f1f5f9);
        border: 1px solid var(--border-color, #e2e8f0);
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pls-thumb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .pls-item-name {
        font-weight: 600;
        font-size: 13.5px;
        color: var(--text-main, #1e293b);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pls-item-meta {
        font-size: 12px;
        color: var(--text-muted, #64748b);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 2px;
    }
    .pls-item-price {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--primary, #3131ff);
        background: var(--primary-soft, rgba(49, 49, 255, 0.08));
        padding: 3px 8px;
        border-radius: 6px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .pls-empty {
        padding: 16px;
        text-align: center;
        color: var(--text-muted, #64748b);
        font-size: 13.5px;
    }
    /* Selected Product Card */
    .pls-selected-box {
        background: var(--background, #f8faff);
        border: 1.5px solid var(--primary, #3131ff);
        border-radius: 10px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .pls-selected-info {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    .pls-selected-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main, #1e293b);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pls-selected-meta {
        font-size: 12px;
        color: var(--text-muted, #64748b);
        margin-top: 2px;
    }
    label span.text-danger,
    label .text-danger {
        display: inline !important;
        margin-left: 3px;
        color: var(--danger, #ef4444);
        font-weight: bold;
    }
</style>

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Offer: {{ $offer->name }}</h2>

        {{-- Success/Error Alert Component --}}
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.offers.update', $offer->id) }}">
            @csrf
            @method('PUT')
            <div class="row">
                {{-- Offer Name --}}
                <div class="col-md-6 mb-3">
                    <label>Offer Name <span class="text-danger">*</span></label>
                    <input class="input-form" name="name" value="{{ old('name', $offer->name) }}" required>
                    @error('name')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Customer Type --}}
                <div class="col-md-6 mb-3">
                    <label>Customer Type <span class="text-danger">*</span></label>
                    <select class="input-form" name="customer_type" id="customerTypeSelect" required>
                        <option value="" disabled {{ old('customer_type', $offer->customer_type) ? '' : 'selected' }}>Select Customer Type</option>
                        <option value="retail" {{ old('customer_type', $offer->customer_type)=='retail' ? 'selected' : '' }}>Retail</option>
                        <option value="wholesale" {{ old('customer_type', $offer->customer_type)=='wholesale' ? 'selected' : '' }}>Wholesale</option>
                    </select>
                    @error('customer_type')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Product Selection with Live Search --}}
                <div class="col-md-6 mb-3">
                    <label>Select Product <span class="text-danger">*</span></label>
                    <input type="hidden" name="product_id" id="selected_product_id" value="{{ old('product_id', $offer->product_id) }}" required>

                    {{-- Selected Product Chip Box --}}
                    <div id="pls-selected-box" class="pls-selected-box" style="display: none;">
                        <div class="pls-selected-info">
                            <div class="pls-thumb" id="pls-selected-thumb">
                                <i class="fas fa-box text-primary"></i>
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <div class="pls-selected-name" id="pls-selected-name"></div>
                                <div class="pls-selected-meta" id="pls-selected-meta"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="pls-clear-btn" style="flex-shrink:0; font-weight:600; padding: 4px 10px;">
                            <i class="fas fa-times me-1"></i> Change
                        </button>
                    </div>

                    {{-- Search Input & Dropdown --}}
                    <div class="pls-container" id="pls-container">
                        <div class="pls-input-wrap">
                            <span class="pls-search-icon"><i class="fas fa-search"></i></span>
                            <input
                                type="text"
                                id="pls-input"
                                class="pls-input"
                                placeholder="Type product name, SKU, or category..."
                                autocomplete="off"
                                inputmode="search">
                        </div>
                        <div id="pls-dropdown" class="pls-dropdown" style="display: none;"></div>
                    </div>
                    @error('product_id')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Coupon Code (Visible only for Retail) --}}
                <div class="col-md-6 mb-3" id="couponCodeContainer" style="{{ old('customer_type', $offer->customer_type) == 'retail' ? '' : 'display: none;' }}">
                    <label>Coupon Code <span class="text-success" style="font-size: 12px;">(Optional, input if you want to give discount via code)</span></label>
                    <input class="input-form" name="coupon_code" id="couponCodeInput" value="{{ old('coupon_code', $offer->coupon_code) }}" placeholder="e.g. CPN01">
                    @error('coupon_code')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Offer Type --}}
                <div class="col-md-6 mb-3">
                    <label>Offer Type <span class="text-danger">*</span></label>
                    <select class="input-form" name="type" required>
                        <option value="percentage" {{ (old('type', $offer->type) == 'percentage') ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ (old('type', $offer->type) == 'fixed') ? 'selected' : '' }}>Fixed Amount (TK)</option>
                    </select>
                    @error('type')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Discount Amount --}}
                <div class="col-md-6 mb-3">
                    <label>Discount Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" class="input-form" name="discount_amount"
                        value="{{ old('discount_amount', $offer->discount_amount) }}" required>
                    @error('discount_amount')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <select class="input-form" name="status">
                        <option value="1" {{ (old('status', $offer->status) == '1') ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (old('status', $offer->status) == '0') ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Date Range Grid --}}
                <div class="col-12 mb-3">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-box">
                            <label>Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="input-form" name="start_date"
                                value="{{ old('start_date', $offer->start_date) }}" required>
                            @error('start_date')<div class="error-text">{{ $message }}</div>@enderror
                        </div>

                        <div class="input-box">
                            <label>End Date <span class="text-danger">*</span></label>
                            <input type="date" class="input-form" name="end_date"
                                value="{{ old('end_date', $offer->end_date) }}" required>
                            @error('end_date')<div class="error-text">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn-submit" type="submit">Update Offer</button>
            </div>
        </form>
    </div>
</div>

@php
    $productsJson = $products->map(function($p) {
        $imgPath = $p->image ? (str_starts_with($p->image, 'uploads/') ? asset($p->image) : asset('uploads/' . $p->image)) : null;
        return [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku ?? '',
            'price' => (float) ($p->price ?? 0),
            'category_name' => $p->category->name ?? 'General',
            'image_url' => $imgPath,
        ];
    })->values();
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allProducts       = {!! json_encode($productsJson) !!};
    const plsInput          = document.getElementById('pls-input');
    const plsDropdown       = document.getElementById('pls-dropdown');
    const plsClearBtn       = document.getElementById('pls-clear-btn');
    const selectedProductId = document.getElementById('selected_product_id');
    const plsContainer      = document.getElementById('pls-container');
    const plsSelectedBox    = document.getElementById('pls-selected-box');
    const plsSelectedName   = document.getElementById('pls-selected-name');
    const plsSelectedMeta   = document.getElementById('pls-selected-meta');
    const plsSelectedThumb  = document.getElementById('pls-selected-thumb');

    let activeIndex = -1;

    function esc(s) {
        return String(s || '')
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function selectProduct(p) {
        selectedProductId.value = p.id;
        plsSelectedName.textContent = p.name;
        plsSelectedMeta.innerHTML = `<span class="badge bg-light text-dark border me-1">${esc(p.category_name)}</span> ${p.sku ? '<span class="me-2">SKU: ' + esc(p.sku) + '</span>' : ''} <strong class="text-primary">৳ ${p.price.toLocaleString(undefined, {minimumFractionDigits: 2})}</strong>`;

        if (p.image_url) {
            plsSelectedThumb.innerHTML = `<img src="${p.image_url}" alt="${esc(p.name)}" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\\'fas fa-box text-primary\\'></i>';">`;
        } else {
            plsSelectedThumb.innerHTML = `<i class="fas fa-box text-primary"></i>`;
        }

        plsContainer.style.display = 'none';
        plsSelectedBox.style.display = 'flex';
        plsDropdown.style.display = 'none';
        plsInput.value = '';
        activeIndex = -1;
    }

    function clearProduct() {
        selectedProductId.value = '';
        plsSelectedBox.style.display = 'none';
        plsContainer.style.display = 'block';
        plsInput.value = '';
        plsInput.focus();
        renderDropdown(allProducts);
    }

    function filterProducts(query) {
        const q = query.trim().toLowerCase();
        if (!q) return allProducts;
        return allProducts.filter(p => {
            return (p.name && p.name.toLowerCase().includes(q)) ||
                   (p.sku && p.sku.toLowerCase().includes(q)) ||
                   (p.category_name && p.category_name.toLowerCase().includes(q));
        });
    }

    function renderDropdown(items) {
        if (!items.length) {
            plsDropdown.innerHTML = '<div class="pls-empty"><i class="fas fa-search me-1"></i> No products found</div>';
            plsDropdown.style.display = 'block';
            return;
        }
        let html = '';
        items.forEach((p, idx) => {
            const thumbHtml = p.image_url 
                ? `<img src="${p.image_url}" alt="${esc(p.name)}" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\\'fas fa-box text-primary\\'></i>';">`
                : `<i class="fas fa-box text-primary" style="font-size: 14px;"></i>`;

            html += `
                <div class="pls-item" data-id="${p.id}" data-index="${idx}">
                    <div class="pls-item-left">
                        <div class="pls-thumb">${thumbHtml}</div>
                        <div style="min-width: 0; flex: 1;">
                            <div class="pls-item-name">${esc(p.name)}</div>
                            <div class="pls-item-meta">
                                <span>${esc(p.category_name)}</span>
                                ${p.sku ? `<span>• SKU: ${esc(p.sku)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="pls-item-price">৳ ${p.price.toLocaleString(undefined, {minimumFractionDigits: 2})}</div>
                </div>
            `;
        });
        plsDropdown.innerHTML = html;
        plsDropdown.style.display = 'block';
        activeIndex = -1;
    }

    if (plsInput) {
        plsInput.addEventListener('input', function () {
            renderDropdown(filterProducts(this.value));
        });

        plsInput.addEventListener('focus', function () {
            renderDropdown(filterProducts(this.value));
        });

        // Keyboard navigation
        plsInput.addEventListener('keydown', function (e) {
            const items = plsDropdown.querySelectorAll('.pls-item');
            if (!items.length || plsDropdown.style.display === 'none') return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                updateActiveItem(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    const id = items[activeIndex].getAttribute('data-id');
                    const found = allProducts.find(p => String(p.id) === String(id));
                    if (found) selectProduct(found);
                }
            } else if (e.key === 'Escape') {
                plsDropdown.style.display = 'none';
            }
        });
    }

    function updateActiveItem(items) {
        items.forEach((item, i) => {
            if (i === activeIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    if (plsDropdown) {
        plsDropdown.addEventListener('click', function (e) {
            const item = e.target.closest('.pls-item');
            if (!item) return;
            const id = item.getAttribute('data-id');
            const found = allProducts.find(p => String(p.id) === String(id));
            if (found) selectProduct(found);
        });
    }

    if (plsClearBtn) {
        plsClearBtn.addEventListener('click', clearProduct);
    }

    document.addEventListener('click', function (e) {
        if (plsContainer && !plsContainer.contains(e.target)) {
            if (plsDropdown) plsDropdown.style.display = 'none';
        }
    });

    // Check old value on validation error or pre-selection
    const oldProductId = selectedProductId ? selectedProductId.value : null;
    if (oldProductId) {
        const preselected = allProducts.find(p => String(p.id) === String(oldProductId));
        if (preselected) selectProduct(preselected);
    }

    // Customer Type toggle for Coupon Code
    const customerTypeSelect = document.getElementById('customerTypeSelect');
    const couponCodeContainer = document.getElementById('couponCodeContainer');

    function toggleCouponField() {
        if (!customerTypeSelect || !couponCodeContainer) return;
        if (customerTypeSelect.value === 'retail') {
            couponCodeContainer.style.display = '';
            couponCodeContainer.classList.add('dynamic-field-anim');
        } else {
            couponCodeContainer.style.display = 'none';
            couponCodeContainer.classList.remove('dynamic-field-anim');
        }
    }

    if (customerTypeSelect) {
        customerTypeSelect.addEventListener('change', toggleCouponField);
        toggleCouponField();
    }
});
</script>
@endpush
@endsection