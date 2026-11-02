@extends(getLayout())

@section('content')
<style>
    .invoice-box {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        padding: 30px;
    }
    .status-badge {
        font-size: 1.1rem;
        padding: 8px 16px;
        border-radius: 50px;
    }
</style>

<div class="container py-4">
    <div class="invoice-box">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">Final Approval BRET{{ $return->id }}</h2>
                <p class="text-muted">Branch: {{ $return->branch->name ?? 'N/A' }} | SR: {{ $return->sr->fullname }}</p>
            </div>
            <div>
                @if($return->status == 'pending_sr')
                    <span class="badge bg-warning text-dark status-badge">Pending SR</span>
                @elseif($return->status == 'pending_manager')
                    <span class="badge bg-info text-white status-badge">Awaiting Your Approval</span>
                @elseif($return->status == 'approved')
                    <span class="badge bg-success status-badge">Approved</span>
                @else
                    <span class="badge bg-danger status-badge">Rejected</span>
                @endif
            </div>
        </div>

        @include('components.alert')

        <div class="row mb-5">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">Customer Information</h5>
                <p class="mb-1"><strong>{{ $return->customer->shop_name ?? 'Retail/No Shop' }}</strong></p>
                <p class="mb-1">{{ $return->customer->address ?? 'N/A' }}</p>
                <p class="mb-1">Current Due: <strong>{{ number_format($return->customer->due ?? 0, 2) }} ৳</strong></p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="fw-bold mb-3">Return Context</h5>
                <p class="mb-1">Original Order: <strong>{{ $return->order_id ?? 'N/A' }}</strong></p>
                <p class="mb-1">Reason: <span class="italic text-muted">{{ $return->reason ?? 'None' }}</span></p>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Return Qty</th>
                        <th class="text-end">Return Price</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-center text-danger fw-bold">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->price, 2) }} ৳</td>
                        <td class="text-end">{{ number_format($item->subtotal, 2) }} ৳</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-primary">
                        <th colspan="3" class="text-end">Total Refund / Due Reduction</th>
                        <th class="text-end">{{ number_format($return->total_amount, 2) }} ৳</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-5">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.return.index') }}" class="btn-smart btn-blue">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
                
                <!-- Delete Permanent Button Triggers Modal -->
                <button type="button" class="btn-smart btn-red" onclick="openDeleteModal()">
                    <i class="fas fa-trash me-1"></i> Delete Permanent
                </button>

                <!-- Hidden Delete Form -->
                <form id="deleteReturnForm" action="{{ route('admin.return.destroy', $return->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            
            @if($return->status == 'pending_manager')
                <form action="{{ route('admin.return.approve', $return->id) }}" method="POST" onsubmit="return confirm('APPROVE this return? Stocks, Orders and Customer Balance will be adjusted immediately.')">
                    @csrf
                    <button type="submit" class="btn-smart btn-green px-5">
                        <i class="fas fa-check-double me-1"></i> Final Approve
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- ==================== Smart Delete Confirmation Modal ==================== -->
<div id="deleteConfirmModal" class="delete-modal-overlay" onclick="handleDeleteModalBackdrop(event)">
    <div class="delete-modal-box">
        <div class="delete-modal-header">
            <div class="d-flex align-items-center gap-2">
                <div class="modal-icon-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-danger">রিটার্ন রিকোয়েস্ট ডিলিট ও রোলব্যাক</h5>
                    <small class="text-muted">Return ID: BRET{{ $return->id }}</small>
                </div>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeDeleteModal()">&times;</button>
        </div>

        <div class="delete-modal-body">
            <!-- Smart Notice Box in Bengali -->
            <div class="smart-notice-box mb-3">
                <div class="notice-header">
                    <i class="fas fa-shield-alt me-1"></i> <strong>সতর্কবার্তা ও রোলব্যাক নীতি:</strong>
                </div>
                <div class="notice-content">
                    <p class="mb-2">
                        আপনি কি নিশ্চিতভাবে এই রিটার্ন রিকোয়েস্টটি <strong>স্থায়ীভাবে Delete</strong> করতে চান? 
                        @if($return->status == 'approved')
                            যেহেতু এটি ইতিপূর্বে অনুমোদিত (Approved) ছিল, ডিলিট করলে এর আর্থিক ও স্টক সমন্বয়গুলো নিম্নরূপে রোলব্যাক (পূর্বাবস্থায় ফেরত) হবে:
                        @else
                            এই রিকোয়েস্টটি এখনো অনুমোদিত হয়নি, তাই ডিলিট করলে শুধুমাত্র এই রেকর্ডটি মুছে যাবে।
                        @endif
                    </p>

                    @if($return->status == 'approved')
                    <div class="rule-box rule-wholesale mb-2">
                        <div class="rule-title">
                            <i class="fas fa-store-alt me-1 text-primary"></i> <strong>হোলসেল / সাধারণ অর্ডার:</strong>
                        </div>
                        <p class="rule-desc mb-0">
                            যদি এটি হোলসেল/সাধারণ অর্ডার হয় এবং কাস্টমারের বকেয়া থাকে, তবে ডিলিট করার পর সমন্বয়কৃত রিফান্ডের টাকা পুনরায় কাস্টমারের বকেয়াতে যোগ হবে (<strong>কাস্টমারের বকেয়া আপডেট হবে</strong>)।
                        </p>
                    </div>

                    <div class="rule-box rule-retail mb-2">
                        <div class="rule-title">
                            <i class="fas fa-cash-register me-1 text-success"></i> <strong>রিটেইল অর্ডার / ক্যাশ রিফান্ড (অন্যথায়):</strong>
                        </div>
                        <p class="rule-desc mb-0">
                            যদি এটি রিটেইল অর্ডার হয় (বা কাস্টমারের বকেয়া না থাকায় ক্যাশ রিফান্ড দেওয়া হয়ে থাকে), তবে রিফান্ডের টাকা সরাসরি <strong>{{ $return->branch->name ?? 'সংশ্লিষ্ট ব্রাঞ্চ' }}</strong> এর ক্যাশ ব্যালেন্সে পুনরায় যোগ হবে (<strong>ব্রাঞ্চের ক্যাশ ব্যালেন্স আপডেট হবে</strong>)।
                        </p>
                    </div>

                    <div class="rule-box rule-stock">
                        <div class="rule-title">
                            <i class="fas fa-boxes me-1 text-warning"></i> <strong>স্টক ও অর্ডার সমন্বয়:</strong>
                        </div>
                        <p class="rule-desc mb-0">
                            ব্রাঞ্চ স্টক থেকে রিটার্নকৃত পণ্যের পরিমাণ হ্রাস পাবে এবং মূল অর্ডারের সংখ্যা ও মূল্য পূর্বাবস্থায় ফিরিয়ে নেওয়া হবে।
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Details Summary Card -->
            <div class="summary-info-card">
                <div class="summary-row">
                    <span class="summary-label"><i class="fas fa-receipt me-1"></i> অর্ডার কোড:</span>
                    <span class="summary-val">{{ $return->order_id ? 'BRS' . $return->order_id : 'N/A' }} 
                        @if($return->order)
                            <span class="badge bg-secondary ms-1">{{ strtoupper($return->order->order_type ?? 'N/A') }}</span>
                        @endif
                    </span>
                </div>
                <div class="summary-row">
                    <span class="summary-label"><i class="fas fa-user-tag me-1"></i> কাস্টমার / ব্রাঞ্চ:</span>
                    <span class="summary-val">{{ $return->customer->shop_name ?? ($return->customer->name ?? 'Retail Customer') }} | {{ $return->branch->name ?? 'N/A' }}</span>
                </div>
                <div class="summary-row border-0">
                    <span class="summary-label"><i class="fas fa-money-bill-wave me-1"></i> মোট রিটার্ন / রিফান্ড মূল্য:</span>
                    <span class="summary-val text-danger fw-bold fs-6">৳ {{ number_format($return->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="delete-modal-footer">
            <button type="button" class="btn-smart btn-gray" onclick="closeDeleteModal()">
                <i class="fas fa-times me-1"></i> বাতিল করুন
            </button>
            <button type="button" class="btn-smart btn-red" onclick="document.getElementById('deleteReturnForm').submit()">
                <i class="fas fa-trash me-1"></i> হ্যাঁ, ডিলিট নিশ্চিত করুন
            </button>
        </div>
    </div>
</div>

<style>
    /* Delete Modal Styles */
    .delete-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.65);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: modalFadeIn 0.2s ease;
    }
    .delete-modal-overlay.show {
        display: flex;
    }
    .delete-modal-box {
        background: var(--section-bg, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 16px;
        width: 100%;
        max-width: 580px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.28);
        overflow: hidden;
        animation: modalSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .delete-modal-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--section-bg, #ffffff);
    }
    .modal-icon-alert {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .modal-close-btn {
        background: transparent;
        border: none;
        font-size: 1.6rem;
        line-height: 1;
        color: var(--text-muted, #94a3b8);
        cursor: pointer;
        transition: color 0.2s;
    }
    .modal-close-btn:hover {
        color: #ef4444;
    }
    .delete-modal-body {
        padding: 22px 24px;
        max-height: 72vh;
        overflow-y: auto;
    }
    .smart-notice-box {
        background: rgba(245, 158, 11, 0.08);
        border: 1px solid rgba(245, 158, 11, 0.28);
        border-radius: 12px;
        padding: 16px;
    }
    .notice-header {
        color: #d97706;
        font-size: 0.95rem;
        margin-bottom: 8px;
    }
    .notice-content {
        font-size: 0.88rem;
        color: var(--text-main, #1e293b);
        line-height: 1.55;
    }
    .rule-box {
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 0.85rem;
    }
    .rule-wholesale {
        background: rgba(59, 130, 246, 0.08);
        border-left: 3px solid #3b82f6;
    }
    .rule-retail {
        background: rgba(16, 185, 129, 0.08);
        border-left: 3px solid #10b981;
    }
    .rule-stock {
        background: rgba(245, 158, 11, 0.08);
        border-left: 3px solid #f59e0b;
    }
    .rule-title {
        font-size: 0.86rem;
        margin-bottom: 3px;
    }
    .rule-desc {
        color: var(--text-muted, #64748b);
        line-height: 1.45;
    }
    .summary-info-card {
        background: var(--background, #f8fafc);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 10px;
        padding: 12px 16px;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 0;
        border-bottom: 1px dashed var(--border-color, #e2e8f0);
        font-size: 0.88rem;
    }
    .summary-label {
        color: var(--text-muted, #64748b);
        font-weight: 500;
    }
    .summary-val {
        font-weight: 600;
        color: var(--text-main, #1e293b);
    }
    .delete-modal-footer {
        padding: 14px 24px;
        border-top: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
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
    @keyframes modalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes modalSlideUp {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>

<script>
    function openDeleteModal() {
        var modal = document.getElementById('deleteConfirmModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeleteModal() {
        var modal = document.getElementById('deleteConfirmModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    function handleDeleteModalBackdrop(event) {
        if (event.target.id === 'deleteConfirmModal') {
            closeDeleteModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endsection
