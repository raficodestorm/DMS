@extends(getLayout())

@section('content')
<p class="show-head">Customer details</p>
<div class="show-card">
    <div class="header-accent">
    </div>

    <div class="content-area">
        <div class="rank-pill">Details of {{ $customer->shop_name }}</div>
        <div class="info-list">
            <div class="info-group">
                <span class="i-label">Customer ID</span>
                <span class="i-value">BRC200{{ $customer->id }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Shop name</span>
                <span class="i-value">{{ $customer->shop_name }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Manager</span>
                <span class="i-value">{{ $customer->manager }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Phone</span>
                <span class="i-value">{{ $customer->phone }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Zone</span>
                <span class="i-value">{{ $customer->branch?->name ?? '_' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Address</span>
                <span class="i-value">{{ $customer->address }}</span>
            </div>

            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $customer->created_at->format('d M Y, h:i A') }}</span>
            </div>

        </div>
        <div class="statement">
            <p class="statement-text">
                "<strong>{{ $customer->shop_name }}</strong> is a verified customer of
                <strong>R.Electric</strong>."
            </p>
        </div>
    </div>

    <div class="card-footer-actions">

        <a href="{{ route('customers.edit', $customer) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>
<a href="{{ route('customers.index') }}" class="back-btn">
    ← Back
</a>

@endsection