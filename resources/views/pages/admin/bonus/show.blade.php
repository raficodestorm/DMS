@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
    <div class="card-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Bonus Details</h2>
            <p class="text-muted">Detailed view of bonus record #{{ $bonus->id }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.bonuses.edit', $bonus) }}" class="btn-smart btn-green">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.bonuses.index') }}" class="btn-smart btn-blue">
                <i class="fas fa-list me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="p-4 border rounded bg-light h-100">
                <div class="mb-4">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Bonus Title</label>
                    <h3 class="fw-bold">{{ $bonus->title }}</h3>
                </div>

                <div class="mb-4">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Description</label>
                    <p class="fs-5 italic text-secondary" style="white-space: pre-line;">
                        {{ $bonus->description ?: 'No description provided.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-4 border rounded bg-white shadow-sm h-100">
                <div class="mb-4">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Total Amount</label>
                    <h2 class="text-primary fw-bold">{{ number_format($bonus->amount, 2) }} ৳</h2>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Type</label>
                    @php
                        $badgeClass = match($bonus->type) {
                            'incentive' => 'bg-info',
                            'cashback' => 'bg-success',
                            'special' => 'bg-warning',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }} fs-6">{{ ucfirst($bonus->type) }}</span>
                </div>

                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Date</label>
                    <p class="fw-bold mb-0"><i class="fas fa-calendar-alt text-muted me-2"></i> {{ $bonus->bonus_date->format('d M Y') }}</p>
                </div>

                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Recorded By</label>
                    <p class="fw-bold mb-0"><i class="fas fa-user text-muted me-2"></i> {{ $bonus->creator->fullname ?? 'Unknown' }}</p>
                </div>

                <div class="mb-0">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Created At</label>
                    <p class="text-muted small mb-0">{{ $bonus->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
