@extends(getLayout())

@section('content')
<style>
    .notice-wrapper {
        max-width: 750px;
        margin: 0 auto;
    }
    .notice-card {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    .refund-hero-box {
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        margin: 25px 0;
        transition: all 0.3s ease;
    }
    .refund-hero-box.has-refund {
        background: linear-gradient(135deg, rgba(22, 163, 74, 0.1) 0%, rgba(34, 197, 94, 0.15) 100%);
        border: 2px solid #22c55e;
    }
    .refund-hero-box.no-refund {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(59, 130, 246, 0.12) 100%);
        border: 2px solid #3b82f6;
    }
    .refund-status-title {
        font-size: 1.1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .refund-amount-big {
        font-size: 2.8rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .stat-badge-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
        align-items: stretch;
    }
    .stat-card {
        background: var(--background);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 16px 14px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100px;
        box-sizing: border-box;
    }
    .stat-card label {
        font-size: 13px;
        color: var(--text-muted);
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        line-height: 1.3;
    }
    .stat-card .val {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--text-main);
    }
    .big-note-box {
        background: var(--background);
        border-left: 5px solid var(--primary);
        border-radius: 12px;
        padding: 20px;
        font-size: 1.1rem;
        line-height: 1.7;
        color: var(--text-main);
        font-weight: 500;
    }
</style>

<div class="container py-4">
    <div class="notice-wrapper">
        <div class="notice-card">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3">
                <div>
                    <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill fw-bold mb-2 d-inline-block">
                        <i class="fas fa-check-circle me-1"></i> Return Approved Notice
                    </span>
                    <h2 class="mb-0 text-dark fw-bold">BRS{{ $return->order_id }} (BRET{{ $return->id }})</h2>
                    <p class="text-muted mb-0 small">Customer: <strong>{{ $return->customer->shop_name ?? 'Retail / Cash Customer' }}</strong></p>
                </div>
                <a href="{{ url()->previous() ?: route('dashboards') }}" class="btn-smart btn-blue">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            {{-- Main Big Highlight: Customer Refund Status --}}
            @if($cashRefund > 0)
                <div class="refund-hero-box has-refund">
                    <div class="refund-status-title text-success">
                        <i class="fas fa-hand-holding-usd me-1"></i> কাস্টমারকে ক্যাশ রিফান্ড প্রদান করুন
                    </div>
                    <div class="refund-amount-big text-success">
                        ৳ {{ number_format($cashRefund, 2) }}
                    </div>
                    <p class="text-muted mt-2 mb-0 fw-medium">
                        কাস্টমারের সমস্ত বকেয়া সমন্বয় করার পর এই পরিমাণ অর্থ ক্যাশ ফেরত দেওয়া হবে।
                    </p>
                </div>
            @else
                <div class="refund-hero-box no-refund">
                    <div class="refund-status-title text-primary">
                        <i class="fas fa-file-invoice-dollar me-1"></i> কোনো ক্যাশ রিফান্ড নেই (বকেয়া থেকে সমন্বয় করা হয়েছে)
                    </div>
                    <div class="refund-amount-big text-primary">
                        ৳ 0.00
                    </div>
                    <p class="text-muted mt-2 mb-0 fw-medium">
                        রিটার্নের সম্পূর্ণ টাকা কাস্টমারের বকেয়া থেকে কেটে সমন্বয় করা হয়েছে।
                    </p>
                </div>
            @endif

            {{-- Big Grid Stats: Due Decrement & Current Due --}}
            <div class="stat-badge-grid">
                <div class="stat-card">
                    <label>মোট রিটার্ন পরিমাণ</label>
                    <div class="val text-primary">৳ {{ number_format($totalReturn, 2) }}</div>
                </div>
                <div class="stat-card">
                    <label>বকেয়া সমন্বয়</label>
                    <div class="val text-danger">৳ {{ number_format($adjustedDue, 2) }}</div>
                </div>
                <div class="stat-card">
                    <label>বর্তমান অবশিষ্ট বকেয়া</label>
                    <div class="val text-dark">৳ {{ number_format($currentDue, 2) }}</div>
                </div>
            </div>

            {{-- Big Explanation Note --}}
            <div class="big-note-box">
                <strong class="d-block text-primary mb-2 fs-6">
                    <i class="fas fa-info-circle me-1"></i> সংক্ষিপ্ত বিবরণ (Notice Explanation):
                </strong>
                {{ $note }}
            </div>
        </div>
    </div>
</div>
@endsection
