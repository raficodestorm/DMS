@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">
    <div class="card-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Global Expense Details</h2>
            <p class="text-muted">Detailed view of global cost record #{{ $cost->id }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.company_costs.edit', $cost->id) }}" class="btn-smart btn-green">
                <i class="fas fa-edit me-1"></i> Edit Global Record
            </a>
            <a href="{{ route('admin.company_costs.index') }}" class="btn-smart btn-blue">
                <i class="fas fa-list me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="p-4 border rounded bg-light h-100">
                <div class="mb-4">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Description</label>
                    <h3 class="fw-bold">{{ $cost->description }}</h3>
                </div>

                <div class="mb-0">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Notes / Reference</label>
                    <p class="fs-5 italic text-secondary" style="white-space: pre-line;">
                        {{ $cost->notes ?: 'No additional notes provided.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-4 border rounded bg-white shadow-sm h-100">
                <div class="mb-4">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Global Amount</label>
                    <h2 class="text-danger fw-bold">{{ number_format($cost->amount, 2) }} ৳</h2>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Category</label>
                    <span class="badge bg-secondary fs-6">{{ ucfirst($cost->category) }}</span>
                </div>

                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Cost Date</label>
                    <p class="fw-bold mb-0"><i class="fas fa-calendar-alt text-muted me-2"></i> {{ $cost->cost_date->format('d M Y') }}</p>
                </div>

                <div class="mb-3">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Recorded By</label>
                    <p class="fw-bold mb-0"><i class="fas fa-user-shield text-muted me-2"></i> {{ $cost->creator->fullname ?? 'Unknown' }}</p>
                </div>

                <div class="mb-0">
                    <label class="small text-muted d-block text-uppercase fw-bold mb-1">Created At</label>
                    <p class="text-muted small mb-0">{{ $cost->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
