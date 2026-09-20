@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">

    <div class="form-card">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2>Edit Slide Banner</h2>
                <p class="text-muted mb-0">Modify slide details, change position or manage connected offers.</p>
            </div>
            <a href="{{ route('admin.slides.index') }}" class="btn btn-outline-secondary" style="font-size: 0.85rem; padding: 6px 14px; border-radius: 8px;">
                <i class="fas fa-arrow-left me-1"></i> Back to Slides
            </a>
        </div>

        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.slides.update', $slide) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- Slide Name --}}
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">Slide Name / Title <span style="color: #dc2626; display: inline; margin-left: 2px;">*</span></label>
                    <input
                        type="text"
                        name="name"
                        class="input-form @error('name') is-invalid @enderror"
                        placeholder="e.g. Summer Mega Discount Deals 2026"
                        value="{{ old('name', $slide->name) }}"
                        required>
                    @error('name')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Position --}}
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Position / Sort Order <span style="color: #dc2626; display: inline; margin-left: 2px;">*</span></label>
                    <input
                        type="number"
                        min="1"
                        name="position"
                        class="input-form @error('position') is-invalid @enderror"
                        placeholder="e.g. 1"
                        value="{{ old('position', $slide->position) }}"
                        required>
                    <small class="text-muted">1 is first, 2 is second, etc.</small>
                    @error('position')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Status <span style="color: #dc2626; display: inline; margin-left: 2px;">*</span></label>
                    <select name="status" class="input-form @error('status') is-invalid @enderror" required>
                        <option value="1" {{ old('status', $slide->status ? '1' : '0') == '1' ? 'selected' : '' }}>Active (Show on Homepage)</option>
                        <option value="0" {{ old('status', $slide->status ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive (Hide)</option>
                    </select>
                    @error('status')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Custom URL fallback --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Custom Destination URL <span class="text-muted fw-normal">(Optional)</span></label>
                    <input
                        type="url"
                        name="custom_url"
                        class="input-form @error('custom_url') is-invalid @enderror"
                        placeholder="https://example.com/custom-page"
                        value="{{ old('custom_url', $slide->custom_url) }}">
                    <small class="text-muted">Only used if no offers are selected below.</small>
                    @error('custom_url')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Multi-Offer Selection Box --}}
                <div class="col-12 mb-4">
                    @php
                        $attachedOfferIds = old('offer_ids', $slide->offers->pluck('id')->toArray());
                    @endphp
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <label class="form-label fw-bold mb-0">
                            Select Connected Retail Offers <span class="text-muted fw-normal">(Select multiple offers for this banner)</span>
                        </label>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-primary" id="selectedOffersCount">{{ count($attachedOfferIds) }} Selected</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllOffersBtn" style="font-size: 0.75rem; padding: 2px 8px;">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllOffersBtn" style="font-size: 0.75rem; padding: 2px 8px;">Deselect All</button>
                        </div>
                    </div>

                    {{-- Search filter within offers --}}
                    <div class="mb-2" style="position: relative;">
                        <input type="text" id="offerSearchFilter" class="input-form" placeholder="Search offers or product name to quickly find & select..." style="padding-left: 30px; font-size: 0.85rem; height: 36px;">
                        <i class="fas fa-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                    </div>

                    {{-- Scrollable Offer List --}}
                    <div id="offerListContainer" style="max-height: 280px; overflow-y: auto; border: 1.5px solid var(--border-color); border-radius: 12px; padding: 12px; background: var(--background);">
                        <div class="row g-2" id="offerCardsGrid">
                            @forelse($retailOffers as $offer)
                                @php
                                    $isPreSelected = in_array($offer->id, $attachedOfferIds);
                                @endphp
                                <div class="col-md-6 offer-item-col" data-offer-text="{{ strtolower($offer->name . ' ' . ($offer->product->name ?? '')) }}">
                                    <label class="offer-selection-card d-flex align-items-start gap-2 p-2 rounded-3 cursor-pointer w-100 mb-0" 
                                           style="border: 1px solid var(--border-color); background: var(--section-bg); transition: all 0.2s ease;">
                                        <input type="checkbox" 
                                               name="offer_ids[]" 
                                               value="{{ $offer->id }}" 
                                               class="form-check-input offer-checkbox mt-1" 
                                               {{ $isPreSelected ? 'checked' : '' }}
                                               onchange="updateSelectedOffersCount()">
                                        <div class="flex-grow-1" style="font-size: 0.84rem;">
                                            <div class="fw-bold text-dark d-flex justify-content-between align-items-center">
                                                <span>{{ $offer->name }}</span>
                                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-size: 0.72rem;">
                                                    @if($offer->type === 'percentage')
                                                        {{ $offer->discount_amount }}% OFF
                                                    @elseif($offer->type === 'fixed')
                                                        ৳{{ number_format($offer->discount_amount, 2) }} OFF
                                                    @else
                                                        Free Shipping
                                                    @endif
                                                </span>
                                            </div>
                                            @if($offer->product)
                                                <div class="text-muted mt-1" style="font-size: 0.78rem;">
                                                    <i class="fas fa-box me-1"></i> Product: <strong>{{ $offer->product->name }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 text-muted">
                                    <i class="fas fa-tags me-1"></i> No active retail offers found.
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <small class="text-muted d-block mt-1">When users click this banner on homepage, all products of the selected offers will be displayed on the campaign page.</small>
                    @error('offer_ids')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Banner Image Upload & Live Preview --}}
                <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold">Banner Image</label>
                    
                    {{-- Current Image Preview --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Current Active Banner:</small>
                        <div style="max-width: 480px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border-color); box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                            <img src="{{ asset($slide->image) }}" alt="{{ $slide->name }}" style="width: 100%; height: auto; display: block;">
                        </div>
                    </div>

                    <div 
                        id="dropZone" 
                        style="border: 2px dashed var(--border-color); border-radius: 12px; padding: 20px; text-align: center; background: var(--background); cursor: pointer; transition: all 0.2s ease;">
                        <input 
                            type="file" 
                            name="image" 
                            id="bannerImageInput" 
                            accept="image/*" 
                            class="d-none">
                        
                        <div id="dropZoneContent">
                            <i class="fas fa-cloud-arrow-up fa-2x mb-2" style="color: var(--primary);"></i>
                            <h6 class="fw-bold mb-1" style="color: var(--text-main);">Upload new image to replace current banner</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Leave blank to keep existing image (Max 5MB)</p>
                        </div>

                        <div id="imagePreviewContainer" class="d-none mt-3">
                            <small class="text-success fw-bold d-block mb-1"><i class="fas fa-check-circle"></i> New Image Selected:</small>
                            <div style="max-width: 480px; margin: 0 auto; border-radius: 10px; overflow: hidden; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.06);">
                                <img id="imagePreview" src="#" alt="New Banner Preview" style="width: 100%; height: auto; display: block;">
                            </div>
                            <button type="button" id="changeImageBtn" class="btn btn-sm btn-outline-secondary mt-2">
                                <i class="fas fa-arrows-rotate me-1"></i> Choose Different Image
                            </button>
                        </div>
                    </div>

                    @error('image')
                        <div class="error-text text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div> <!-- end row -->

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.slides.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Cancel</a>
                <button type="submit" class="btn-smart btn-blue">
                    <i class="fas fa-save me-1"></i> Update Slide Banner
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('dropZone');
    const bannerInput = document.getElementById('bannerImageInput');
    const dropContent = document.getElementById('dropZoneContent');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const changeBtn = document.getElementById('changeImageBtn');

    dropZone.addEventListener('click', function (e) {
        if (e.target !== changeBtn && !changeBtn.contains(e.target)) {
            bannerInput.click();
        }
    });

    changeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        bannerInput.click();
    });

    bannerInput.addEventListener('change', function () {
        handleFiles(this.files);
    });

    // Drag & Drop
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = 'var(--primary)';
            dropZone.style.background = 'var(--primary-soft)';
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = 'var(--border-color)';
            dropZone.style.background = 'var(--background)';
        });
    });

    dropZone.addEventListener('drop', function (e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        bannerInput.files = files;
        handleFiles(files);
    });

    function handleFiles(files) {
        if (files && files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                dropContent.classList.add('d-none');
                previewContainer.classList.remove('d-none');
            };
            reader.readAsDataURL(files[0]);
        }
    }

    // Offer Selection Helpers
    const offerSearch = document.getElementById('offerSearchFilter');
    const offerItems = document.querySelectorAll('.offer-item-col');
    const selectAllBtn = document.getElementById('selectAllOffersBtn');
    const deselectAllBtn = document.getElementById('deselectAllOffersBtn');
    const countBadge = document.getElementById('selectedOffersCount');

    window.updateSelectedOffersCount = function() {
        const checked = document.querySelectorAll('.offer-checkbox:checked').length;
        countBadge.innerText = checked + ' Selected';
    };

    if (offerSearch) {
        offerSearch.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            offerItems.forEach(item => {
                const text = item.getAttribute('data-offer-text');
                item.style.display = (!term || text.includes(term)) ? 'block' : 'none';
            });
        });
    }

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            document.querySelectorAll('.offer-checkbox').forEach(cb => cb.checked = true);
            updateSelectedOffersCount();
        });
    }

    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            document.querySelectorAll('.offer-checkbox').forEach(cb => cb.checked = false);
            updateSelectedOffersCount();
        });
    }
});
</script>
@endpush
