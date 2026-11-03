@php $isAdmin = auth()->user()->role === 'admin'; @endphp

{{-- Ranking Position Header --}}
<div class="position-hero-card mb-3 p-3 rounded-3" style="background: var(--section-bg); border: 1px solid var(--border-color);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-2">
            @if($rank == 1)
                <span class="rank-badge rank-1 px-3 py-1 fs-6">
                    <i class="fas fa-crown text-warning me-1"></i> Rank #1 Champion
                </span>
            @elseif($rank == 2)
                <span class="rank-badge rank-2 px-3 py-1 fs-6">
                    <i class="fas fa-medal me-1" style="color: #94a3b8;"></i> Rank #2 Runner Up
                </span>
            @elseif($rank == 3)
                <span class="rank-badge rank-3 px-3 py-1 fs-6">
                    <i class="fas fa-medal me-1" style="color: #cd7f32;"></i> Rank #3 Third Place
                </span>
            @else
                <span class="rank-badge rank-normal px-3 py-1 fs-6">
                    <i class="fas fa-award me-1 text-primary"></i> Rank #{{ $rank }}
                </span>
            @endif
            <span class="badge bg-light text-muted border">BRC200{{ $customer->id }}</span>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
            <i class="fas fa-store me-1"></i> Wholesale
        </span>
    </div>

    <div class="d-flex align-items-center gap-3 mt-2">
        <div class="customer-avatar-lg">
            <i class="fas fa-store"></i>
        </div>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-1" style="color: var(--text-main);">{{ $customer->shop_name }}</h4>
            <div class="text-muted small">
                Ranking Window: <span class="fw-semibold">{{ $sinceDate->format('d M') }} - {{ now()->format('d M, Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Ranking Performance: Orders & Order Amount --}}
<div class="row g-2 mb-3">
    {{-- Order Amount --}}
    <div class="col-sm-6">
        <div class="p-3 rounded-3 text-center" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
            <div class="text-muted small fw-semibold">
                <i class="fas fa-money-bill-wave text-success me-1"></i> 30-Day Order Amount
            </div>
            <div class="fs-4 fw-bold text-success my-1">
                ৳ {{ number_format($totalAmount30Days, 2) }}
            </div>
            <span class="text-muted" style="font-size: 0.75rem;">Total Wholesale Purchases</span>
        </div>
    </div>

    {{-- Orders Count --}}
    <div class="col-sm-6">
        <div class="p-3 rounded-3 text-center" style="background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.25);">
            <div class="text-muted small fw-semibold">
                <i class="fas fa-box text-primary me-1"></i> 30-Day Total Orders
            </div>
            <div class="fs-4 fw-bold text-primary my-1">
                {{ $totalOrders30Days }} <span class="fs-6 fw-normal">Orders</span>
            </div>
            <span class="text-muted" style="font-size: 0.75rem;">Completed in Last 30 Days</span>
        </div>
    </div>
</div>

{{-- Customer Short Info Details --}}
<div class="card p-3 rounded-3 mb-3" style="background: var(--section-bg); border: 1px solid var(--border-color);">
    <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: var(--text-main); font-size: 0.9rem;">
        <i class="fas fa-id-card text-primary me-2"></i> Customer Short Info
    </h6>

    <div class="row g-2" style="font-size: 0.86rem;">
        {{-- Manager --}}
        <div class="col-6">
            <div class="text-muted small">Manager / Contact</div>
            <div class="fw-semibold text-truncate" title="{{ $customer->manager ?? 'N/A' }}">
                <i class="fas fa-user-tie text-secondary me-1"></i> {{ $customer->manager ?? 'N/A' }}
            </div>
        </div>

        {{-- Phone --}}
        <div class="col-6">
            <div class="text-muted small">Phone Number</div>
            <div class="fw-semibold">
                @if($customer->phone)
                    <i class="fas fa-phone-alt text-success me-1"></i>
                    <a href="tel:{{ $customer->phone }}" class="text-decoration-none text-dark">{{ $customer->phone }}</a>
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </div>
        </div>

        {{-- Area Branch --}}
        <div class="{{ $isAdmin ? 'col-6' : 'col-12' }} pt-2 border-top">
            <div class="text-muted small">Branch / Area</div>
            <div class="fw-semibold">
                <i class="fas fa-code-branch text-primary me-1"></i> {{ $customer->branch->name ?? 'N/A' }}
            </div>
        </div>

        {{-- Referred SR — Admin only --}}
        @if($isAdmin)
            <div class="col-6 pt-2 border-top">
                <div class="text-muted small">Referred SR</div>
                <div class="fw-semibold text-truncate">
                    <i class="fas fa-user-tag me-1" style="color: #8b5cf6;"></i> {{ $customer->sr->fullname ?? 'Not Assigned' }}
                </div>
            </div>
        @endif

        {{-- Address --}}
        <div class="col-12 pt-2 border-top">
            <div class="text-muted small">Address</div>
            <div class="fw-semibold">
                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                {{ $customer->address ?? 'N/A' }}@if($customer->city), {{ $customer->city }}@endif
            </div>
        </div>

        {{-- Current Due — Admin only --}}
        @if($isAdmin)
            <div class="col-12 pt-2 border-top d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Current Due Balance:</span>
                    <strong class="ms-1 {{ ($customer->due ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                        ৳ {{ number_format($customer->due ?? 0, 2) }}
                    </strong>
                </div>
                <span class="badge {{ ($customer->due ?? 0) > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}" style="font-size: 0.72rem;">
                    {{ ($customer->due ?? 0) > 0 ? 'Due Pending' : 'Clear' }}
                </span>
            </div>
        @endif
    </div>
</div>

{{-- Footer Actions --}}
<div class="d-flex justify-content-between align-items-center pt-2">
    <button type="button" class="btn-smart btn-gray" onclick="closeRankingPositionModal()">
        <i class="fas fa-times me-1"></i> Close
    </button>
    
</div>
