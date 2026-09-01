@extends(getLayout())

@section('content')
<style>
    .form-card {
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        padding: 20px;
    }
    .order-item-row {
        background: var(--background);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
    }
    .order-item-row.selected {
        border-color: var(--primary);
        background: var(--primary-soft);
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    /* ── Order Live Search (OLS) ──────────────────────────────────────── */
    .ols-container { position: relative; }
    .ols-input-wrap {
        display: flex; align-items: center;
        background: var(--section-bg);
        border: 2px solid var(--border-color);
        border-radius: 10px; padding: 0 14px;
        transition: border-color .2s, box-shadow .2s;
    }
    .ols-input-wrap:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(49,49,255,.12);
    }
    .ols-search-icon { color: var(--text-muted); margin-right: 10px; font-size: 15px; }
    .ols-input {
        flex: 1; border: none; outline: none; background: transparent;
        padding: 12px 0; font-size: 15px; color: var(--text-main);
    }
    .ols-input::placeholder { color: var(--text-muted); }
    .ols-dropdown {
        position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        background: var(--section-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,.13);
        max-height: 280px; overflow-y: auto; z-index: 9999;
    }
    .ols-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 14px; cursor: pointer;
        border-bottom: 1px solid var(--border-color);
        transition: background .15s;
    }
    .ols-item:last-child { border-bottom: none; }
    .ols-item:hover, .ols-item:active { background: var(--primary-soft, #eef2ff); }
    .ols-item-code { font-weight: 700; font-size: 14px; color: var(--primary); }
    .ols-item-shop { font-weight: 600; font-size: 14px; color: var(--text-main); }
    .ols-item-date {
        font-size: 12px; color: var(--text-muted);
        background: var(--background); padding: 3px 10px;
        border-radius: 20px; white-space: nowrap; margin-left: 8px;
    }
    .ols-empty { padding: 16px; text-align: center; color: var(--text-muted); font-size: 14px; }
    .ols-selected-box {
        background: var(--section-bg);
        border: 2px solid var(--primary);
        border-radius: 10px;
        padding: 10px 14px;
        display: flex; align-items: center; justify-content: space-between;
    }
</style>

<div class="container py-4">
    <div class="form-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0"><i class="fas fa-undo text-primary"></i> Create Return Request</h2>
            <a href="{{ route('manager.return.index') }}" class="btn-smart btn-blue">
                <i class="fas fa-list me-1"></i> My Returns
            </a>
        </div>

        @include('components.alert')

        {{-- Step 1: Order Selection with Smart Live Search --}}
        <form action="{{ route('manager.return.create') }}" method="GET" class="mb-4" id="orderSelectForm">
            <label class="form-label fw-bold">1. Select Order</label>
            <input type="hidden" name="order_id" id="selected_order_id" value="{{ request('order_id') }}">

            {{-- Selected Order Display Box --}}
            <div id="ols-selected-box" class="ols-selected-box mb-2" style="{{ $selectedOrder ? 'display: flex;' : 'display: none;' }}">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <i class="fas fa-file-invoice text-primary fs-5"></i>
                    <strong id="ols-selected-code" class="fs-6 text-primary">
                        {{ $selectedOrder ? 'BRS' . $selectedOrder->id : '' }}
                    </strong>
                    <span class="text-dark fw-bold" id="ols-selected-shop">
                        {{ $selectedOrder?->customer?->shop_name }}
                    </span>
                    <span class="ols-item-date" id="ols-selected-date">
                        {{ $selectedOrder?->created_at ? $selectedOrder->created_at->format('d M Y') : '' }}
                    </span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="ols-clear-btn">
                    <i class="fas fa-times me-1"></i> Change Order
                </button>
            </div>

            {{-- Smart Live Search Input Container --}}
            <div class="ols-container" id="ols-container" style="{{ $selectedOrder ? 'display: none;' : 'display: block;' }}">
                <div class="ols-input-wrap">
                    <span class="ols-search-icon"><i class="fas fa-search"></i></span>
                    <input
                        type="text"
                        id="ols-input"
                        class="ols-input"
                        placeholder="Type Order ID (e.g. BRS12 or 12) or Shop Name (or 2 spaces for all orders)..."
                        autocomplete="off"
                        inputmode="search">
                </div>
                <div id="ols-dropdown" class="ols-dropdown" style="display:none"></div>
            </div>
        </form>

        @if($selectedOrder)
            <form action="{{ route('manager.return.store') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $selectedOrder->id }}">

                <div class="mb-4">
                    <label class="form-label fw-bold">2. Select Items to Return</label>
                    <div id="items-wrapper">
                        @foreach($selectedOrder->items as $item)
                            @if($item->available_to_return > 0)
                                <div class="order-item-row">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <strong>{{ $item->product->name }}</strong><br>
                                            <small class="text-muted">Purchased: {{ $item->quantity }} | Price: {{ number_format($item->selling_rate, 2) }} ৳</small>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small text-muted d-block">Available to Return: {{ $item->available_to_return }}</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" 
                                                       name="items[{{ $loop->index }}][quantity]" 
                                                       class="form-control return-qty" 
                                                       data-price="{{ $item->selling_rate }}"
                                                       min="0" 
                                                       max="{{ $item->available_to_return }}" 
                                                       value="0"
                                                       oninput="calculateTotals()">
                                                <span class="input-group-text">Qty</span>
                                            </div>
                                            <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <label class="small text-muted d-block">Subtotal</label>
                                            <span class="item-subtotal">0.00</span> ৳
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Reason for Return</label>
                    <textarea name="reason" class="input-form" rows="3" placeholder="Explain why these items are being returned..."></textarea>
                </div>

                <div class="p-summary-card mb-4" style="background: var(--background); padding: 15px; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-muted">Total Return Amount</p>
                            <h3 class="mb-0 text-primary" id="total-display">0.00 ৳</h3>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-smart btn-green py-2 px-4">
                    Submit Return Request <i class="fas fa-paper-plane ms-1"></i>
                </button>
            </form>
        @elseif(request('order_id'))
            <div class="alert alert-info">
                No items available for return in this order. They might have been already returned.
            </div>
        @endif
    </div>
</div>
@endsection

@php
    $ordersJson = $orders->map(function($o) {
        return [
            'id' => $o->id,
            'code' => 'BRS' . $o->id,
            'shop_name' => $o->customer->shop_name ?? 'N/A',
            'date' => $o->created_at ? $o->created_at->format('d M Y') : '',
        ];
    })->values();
@endphp

@push('scripts')
<script>
    function calculateTotals() {
        let total = 0;
        document.querySelectorAll('.return-qty').forEach(input => {
            let qty = parseInt(input.value) || 0;
            let price = parseFloat(input.dataset.price);
            let subtotal = qty * price;
            
            let row = input.closest('.order-item-row');
            if (qty > 0) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }

            input.closest('.row').querySelector('.item-subtotal').innerText = subtotal.toFixed(2);
            total += subtotal;
        });
        document.getElementById('total-display').innerText = total.toFixed(2) + ' ৳';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Pre-loaded orders data for smart live searching
        var allOrders = {!! json_encode($ordersJson) !!};

        var olsInput        = document.getElementById('ols-input');
        var olsDropdown     = document.getElementById('ols-dropdown');
        var olsClearBtn     = document.getElementById('ols-clear-btn');
        var selectedOrderId  = document.getElementById('selected_order_id');
        var orderSelectForm  = document.getElementById('orderSelectForm');

        function esc(s) {
            return String(s || '')
                .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
                .replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        if (olsInput) {
            olsInput.addEventListener('input', function () {
                var raw = this.value;
                var q = raw.trim().toLowerCase();

                // Rule: 2 or more spaces shows ALL orders; 2+ characters shows matched orders
                var isShowAll = raw.length >= 2 && raw.trim() === '';
                var isSearchMatch = q.length >= 2;

                if (!isShowAll && !isSearchMatch) {
                    olsDropdown.style.display = 'none';
                    olsDropdown.innerHTML = '';
                    return;
                }

                var filtered = [];
                if (isShowAll) {
                    filtered = allOrders;
                } else {
                    filtered = allOrders.filter(function (o) {
                        var idStr = String(o.id).toLowerCase();
                        var codeStr = String(o.code).toLowerCase();
                        var shopStr = String(o.shop_name).toLowerCase();

                        return idStr.includes(q) || codeStr.includes(q) || shopStr.includes(q);
                    });
                }

                if (!filtered.length) {
                    olsDropdown.innerHTML = '<div class="ols-empty"><i class="fas fa-box-open me-1"></i> No delivered orders found</div>';
                    olsDropdown.style.display = 'block';
                    return;
                }

                var html = '';
                filtered.forEach(function (o) {
                    html += '<div class="ols-item"'
                        + ' data-id="' + o.id + '"'
                        + ' data-code="' + esc(o.code) + '"'
                        + ' data-shop="' + esc(o.shop_name) + '"'
                        + ' data-date="' + esc(o.date) + '">'
                        + '<div>'
                        + '<span class="ols-item-code me-2">' + esc(o.code) + '</span>'
                        + '<span class="ols-item-shop">' + esc(o.shop_name) + '</span>'
                        + '</div>'
                        + '<span class="ols-item-date">' + esc(o.date) + '</span>'
                        + '</div>';
                });

                olsDropdown.innerHTML = html;
                olsDropdown.style.display = 'block';
            });

            olsDropdown.addEventListener('click', function (e) {
                var item = e.target.closest('.ols-item');
                if (!item) return;

                var id = item.dataset.id;
                selectedOrderId.value = id;
                orderSelectForm.submit();
            });

            if (olsClearBtn) {
                olsClearBtn.addEventListener('click', function () {
                    selectedOrderId.value = '';
                    window.location.href = "{{ route('manager.return.create') }}";
                });
            }

            document.addEventListener('click', function (e) {
                if (!e.target.closest('#ols-container')) {
                    olsDropdown.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush
