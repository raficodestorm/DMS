@extends('layouts.adminlayout')

@section('content')
<p class="show-head">Supplier details</p>
<div class="show-card">
    <div class="header-accent">
    </div>

    <div class="content-area">
        @if($supplier->image)
        <div class="text-center mb-3">
            <img src="{{ asset($supplier->image) }}" alt="{{ $supplier->name }}" style="width: 120px; height: 85px; object-fit: cover; border-radius: 8px; border: 2px solid var(--primary, #1d4ed8);">
        </div>
        @endif
        <div class="rank-pill">Details of {{ $supplier->company_name }} supplier</div>
        <div class="info-list">
            <div class="info-group">
                <span class="i-label">Name</span>
                <span class="i-value">{{ $supplier->name }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Company Name</span>
                <span class="i-value">{{ $supplier->company_name }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Phone</span>
                <span class="i-value">{{ $supplier->phone }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Email</span>
                <span class="i-value">{{ $supplier->email ?? 'N/A' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Due Amount</span>
                <span class="i-value text-danger font-weight-bold">{{ number_format($supplier->due, 2) }} TK</span>
            </div>
            <div class="info-group">
                <span class="i-label">Address</span>
                <span class="i-value">{{ $supplier->address ?? 'N/A' }}</span>
            </div>

            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $supplier->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</span>
            </div>

        </div>
        <div class="statement">
            <p class="statement-text">
                "<strong>{{ $supplier->company_name }}</strong> is a verified supplier of
                <strong>{{ config('app.name') }}</strong>.
            </p>
        </div>
    </div>

    <div class="card-footer-actions">

        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>
<a href="{{ route('admin.suppliers.index') }}" class="back-btn">
    ← Back
</a>

@endsection