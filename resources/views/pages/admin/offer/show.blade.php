@extends('layouts.adminlayout')

@section('content')
<p class="show-head">Offer Details</p>
<div class="show-card">
    <div class="header-accent">

    </div>

    <div class="content-area">
        <div class="rank-pill">Details of {{ $offer->name }}</div>
        <div class="info-list">
            <div class="info-group">
                <span class="i-label">Offer Name</span>
                <span class="i-value">{{ $offer->name }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Applied Product</span>
                <span class="i-value">{{ $offer->product->name ?? 'N/A' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Discount Type</span>
                <span class="i-value" style="text-transform: capitalize;">{{ $offer->type }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Discount Amount</span>
                <span class="i-value">
                    <strong>{{ $offer->type == 'percentage' ? $offer->discount_amount . '%' :
                        number_format($offer->discount_amount, 2) . ' TK' }}</strong>
                </span>
            </div>
            <div class="info-group">
                <span class="i-label">Start Date</span>
                <span class="i-value">{{ \Carbon\Carbon::parse($offer->start_date)->format('d M Y') }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">End Date</span>
                <span class="i-value">{{ \Carbon\Carbon::parse($offer->end_date)->format('d M Y') }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Status</span>
                <span class="i-value {{ $offer->status == 1 ? 'text-success' : 'text-danger' }}">
                    {{ $offer->status == 1 ? '● Active' : '● Inactive' }}
                </span>
            </div>

            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $offer->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A')
                    }}</span>
            </div>

        </div>

        <div class="statement">
            <p class="statement-text">
                <strong>Offer Summary</strong>
                This offer provides a {{ $offer->type }} discount of {{ $offer->discount_amount }}{{ $offer->type ==
                'percentage' ? '%' : ' TK' }}
                on {{ $offer->product->name ?? 'the selected product' }}.
                Valid from {{ \Carbon\Carbon::parse($offer->start_date)->format('d M') }} until {{
                \Carbon\Carbon::parse($offer->end_date)->format('d M Y') }}.
            </p>
        </div>
    </div>

    <div class="card-footer-actions">
        <a href="{{ route('admin.offers.edit', $offer->id) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure you want to delete this offer?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none; background: none;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>

<div style="text-align: center; margin-top: 20px;">
    <a href="{{ route('admin.offers.index') }}" style="color: var(--text-muted); text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

@endsection