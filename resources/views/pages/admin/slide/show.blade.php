@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">

    <div class="manage-card" style="max-width: 900px; margin: 0 auto;">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">{{ $slide->name }}</h2>
                <span class="text-muted">Created on {{ $slide->created_at->format('F d, Y h:i A') }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.slides.edit', $slide) }}" class="btn-smart btn-blue">
                    <i class="fas fa-edit me-1"></i> Edit Slide
                </a>
                <a href="{{ route('admin.slides.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Slides
                </a>
            </div>
        </div>

        {{-- Banner Display --}}
        <div class="mb-4">
            <label class="form-label fw-bold d-block text-muted">Active Banner Graphic:</label>
            <div style="width: 100%; border-radius: 14px; overflow: hidden; border: 1px solid var(--border-color); box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <img src="{{ asset($slide->image) }}" alt="{{ $slide->name }}" style="width: 100%; height: auto; display: block;">
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6">
                <div class="p-3" style="background: var(--background); border-radius: 10px; border: 1px solid var(--border-color);">
                    <small class="text-muted d-block mb-1">Display Position</small>
                    <span class="fw-bold" style="font-size: 1.1rem; color: var(--primary);">
                        <i class="fas fa-arrow-down-1-9 me-1"></i> #{{ $slide->position }}
                    </span>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="p-3" style="background: var(--background); border-radius: 10px; border: 1px solid var(--border-color);">
                    <small class="text-muted d-block mb-1">Status</small>
                    <span class="badge" style="background: {{ $slide->status ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $slide->status ? '#059669' : '#dc2626' }}; padding: 6px 14px; border-radius: 20px; font-weight: 700;">
                        <i class="fas {{ $slide->status ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                        {{ $slide->status ? 'Active on Homepage' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="p-3" style="background: var(--background); border-radius: 10px; border: 1px solid var(--border-color);">
                    <small class="text-muted d-block mb-1">Destination URL</small>
                    <a href="{{ $slide->target_url }}" target="_blank" class="fw-semibold text-primary text-truncate d-block" style="font-size: 0.9rem;" title="{{ $slide->target_url }}">
                        <i class="fas fa-external-link-alt me-1"></i> Preview Campaign Page
                    </a>
                </div>
            </div>
        </div>

        {{-- Connected Offers List --}}
        <div class="card border-0 p-3" style="background: var(--section-bg); border-radius: 12px; border: 1px solid var(--border-color) !important;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="color: var(--text-main);">
                    <i class="fas fa-tags text-accent me-1"></i> Connected Retail Offers ({{ $slide->offers->count() }})
                </h5>
                @if($slide->offers->count() > 0)
                    <a href="{{ route('slide.offers', $slide) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> View Live Campaign
                    </a>
                @endif
            </div>

            @if($slide->offers && $slide->offers->count() > 0)
                <div class="row g-2">
                    @foreach($slide->offers as $offer)
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: var(--background); border: 1px solid var(--border-color);">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark">{{ $offer->name }}</strong>
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669;">
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
                                    <small class="text-muted d-block">
                                        <i class="fas fa-box me-1"></i> Product: <strong>{{ $offer->product->name }}</strong> (SKU: {{ $offer->product->sku }})
                                    </small>
                                @endif
                                @if($offer->end_date)
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-clock me-1"></i> Expires: {{ \Carbon\Carbon::parse($offer->end_date)->format('d M, Y') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0 py-3 text-center">
                    <i class="fas fa-info-circle me-1"></i> No specific offers attached. This slide links to the main Shop catalog.
                </p>
            @endif
        </div>

    </div>

</div>

@endsection
