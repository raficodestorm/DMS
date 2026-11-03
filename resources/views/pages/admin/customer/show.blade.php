@extends('layouts.adminlayout')

@section('content')
<div class="mb-3">
    @include('components.alert')
</div>
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
                <span class="i-value">{{ $customer->shop_name ?? 'N/A'  }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Manager</span>
                <span class="i-value">{{ $customer->manager ?? 'N/A' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Phone</span>
                <span class="i-value">{{ $customer->phone ?? 'N/A' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Customer Type</span>
                <span class="i-value d-flex align-items-center gap-2">
                    @if($customer->customer_type === 'retail')
                        <span class="purple-type-badge">Retail</span>
                    @else
                        <span class="emerald-type-badge">Wholesale</span>
                    @endif
                    <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none ms-1" onclick="openChangeTypeModal()" title="Change Customer Type" style="font-size: 0.85rem; color: var(--primary);">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                </span>
            </div>
            <div class="info-group">
                <span class="i-label">Zone</span>
                <span class="i-value">{{ $customer->branch?->name ?? '_' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Refered SR</span>
                <span class="i-value">{{ $customer->sr?->fullname ?? '_' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Country</span>
                <span class="i-value">{{ $customer->country ?? 'N/A' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">City</span>
                <span class="i-value">{{ $customer->city ?? 'N/A' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Address</span>
                <span class="i-value">{{ $customer->address ?? 'N/A' }}</span>
            </div>

            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $customer->created_at->format('d M Y, h:i A') }}</span>
            </div>

            <div class="info-group">
                <span class="i-label">Due</span>
                <span class="i-value">{{ $customer->due ?? 'N/A' }}</span>
            </div>

        </div>
        <div class="statement">
            <p class="statement-text">
                "<strong>{{ $customer->shop_name }}</strong> is a verified customer of
                <strong>{{ config('app.name') }}</strong>."
            </p>
        </div>
    </div>

    <div class="card-footer-actions">

        <button type="button" class="icon-btn change-type-icon" onclick="openChangeTypeModal()" title="Change Customer Type" style="border: none;">
            <i class="fa-solid fa-user-tag"></i>
        </button>

        <a href="{{ route('admin.customers.edit', $customer) }}" class="icon-btn edit-icon" title="Edit Customer">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;" title="Delete Customer">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>
<a href="{{ route('admin.customers.index') }}" class="back-btn">
    ← Back
</a>

<!-- ==================== Change Customer Type Modal ==================== -->
<div id="changeTypeModal" class="custom-modal-overlay" onclick="handleChangeTypeModalBackdrop(event)">
    <div class="custom-modal-box">
        <div class="custom-modal-header">
            <div class="d-flex align-items-center gap-2">
                <div class="modal-icon-badge">
                    <i class="fa-solid fa-user-tag"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Customer Type পরিবর্তন করুন</h5>
                    <small class="text-muted">{{ $customer->shop_name }} (BRC200{{ $customer->id }})</small>
                </div>
            </div>
            <button type="button" class="custom-modal-close-btn" onclick="closeChangeTypeModal()">&times;</button>
        </div>

        <form action="{{ route('admin.customers.change-type', $customer->id) }}" method="POST">
            @csrf
            <div class="custom-modal-body">
                <div class="current-type-banner mb-3">
                    <span class="text-muted">বর্তমান টাইপ:</span>
                    @if($customer->customer_type === 'retail')
                        <span class="purple-type-badge ms-1">Retail</span>
                    @else
                        <span class="emerald-type-badge ms-1">Wholesale</span>
                    @endif
                </div>

                <label class="form-label fw-bold mb-2">নতুন Customer Type নির্বাচন করুন:</label>

                <div class="type-selection-cards">
                    {{-- Wholesale Option --}}
                    <label class="type-card {{ ($customer->customer_type ?? 'wholesale') === 'wholesale' ? 'active' : '' }}">
                        <input type="radio" name="customer_type" value="wholesale" {{ ($customer->customer_type ?? 'wholesale') === 'wholesale' ? 'checked' : '' }} onchange="highlightTypeCard(this)">
                        <div class="type-card-content">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold fs-6 text-primary"><i class="fa-solid fa-store me-1"></i> Wholesale (হোলসেল)</span>
                                <span class="emerald-type-badge">Wholesale</span>
                            </div>
                            <p class="mb-0 text-muted small">
                                পাইকারি কাস্টমার। হোলসেল অফার ও ডিলারশিপ রেটের জন্য প্রযোজ্য।
                            </p>
                        </div>
                    </label>

                    {{-- Retail Option --}}
                    <label class="type-card {{ $customer->customer_type === 'retail' ? 'active' : '' }}">
                        <input type="radio" name="customer_type" value="retail" {{ $customer->customer_type === 'retail' ? 'checked' : '' }} onchange="highlightTypeCard(this)">
                        <div class="type-card-content">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold fs-6 text-purple" style="color: #8b5cf6;"><i class="fa-solid fa-basket-shopping me-1"></i> Retail (রিটেইল)</span>
                                <span class="purple-type-badge">Retail</span>
                            </div>
                            <p class="mb-0 text-muted small">
                                খুচরা কাস্টমার। রেগুলার রিটেইল প্রাইসিং ও রিটেইল অফারের জন্য প্রযোজ্য।
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="custom-modal-footer">
                <button type="button" class="btn-smart btn-gray" onclick="closeChangeTypeModal()">
                    বাতিল করুন
                </button>
                <button type="submit" class="btn-smart btn-green">
                    <i class="fa-solid fa-check me-1"></i> আপডেট করুন
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .change-type-icon {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }
    .change-type-icon:hover {
        background: #10b981;
        color: #fff;
        transform: translateY(-2px);
    }
    .custom-modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.65);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center; justify-content: center;
        padding: 16px;
        animation: fadeIn 0.2s ease;
    }
    .custom-modal-overlay.show {
        display: flex;
    }
    .custom-modal-box {
        background: var(--section-bg, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 16px;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 15px 45px rgba(0,0,0,0.25);
        overflow: hidden;
        animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .custom-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--section-bg, #ffffff);
    }
    .modal-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .custom-modal-close-btn {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        line-height: 1;
        color: var(--text-muted, #94a3b8);
        cursor: pointer;
        transition: color 0.2s;
    }
    .custom-modal-close-btn:hover {
        color: #ef4444;
    }
    .custom-modal-body {
        padding: 20px;
    }
    .current-type-banner {
        background: var(--background, #f8fafc);
        border: 1px solid var(--border-color, #e2e8f0);
        padding: 10px 14px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.9rem;
    }
    .type-selection-cards {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .type-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 14px;
        border: 1.5px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--section-bg, #ffffff);
    }
    .type-card:hover {
        border-color: var(--primary, #3b82f6);
        background: rgba(59, 130, 246, 0.02);
    }
    .type-card.active {
        border-color: var(--primary, #3b82f6);
        background: rgba(59, 130, 246, 0.05);
    }
    .type-card input[type="radio"] {
        margin-top: 4px;
        accent-color: var(--primary, #3b82f6);
    }
    .type-card-content {
        flex: 1;
    }
    .custom-modal-footer {
        padding: 14px 20px;
        border-top: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: var(--section-bg, #ffffff);
    }
    .btn-gray {
        background: #64748b;
        color: #fff;
    }
    .btn-gray:hover {
        background: #475569;
        color: #fff;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(16px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>

<script>
    function openChangeTypeModal() {
        const modal = document.getElementById('changeTypeModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeChangeTypeModal() {
        const modal = document.getElementById('changeTypeModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    function handleChangeTypeModalBackdrop(event) {
        if (event.target.id === 'changeTypeModal') {
            closeChangeTypeModal();
        }
    }

    function highlightTypeCard(radio) {
        document.querySelectorAll('.type-card').forEach(card => card.classList.remove('active'));
        if (radio.checked) {
            radio.closest('.type-card').classList.add('active');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeChangeTypeModal();
        }
    });
</script>
@endsection