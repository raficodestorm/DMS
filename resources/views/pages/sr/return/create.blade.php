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
</style>

<div class="container py-4">
    <div class="form-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0"><i class="fas fa-undo text-primary"></i> Create Return Request</h2>
            <a href="{{ route('sr.return.index') }}" class="btn-smart btn-blue">
                <i class="fas fa-list me-1"></i> My Returns
            </a>
        </div>

        @include('components.alert')

        <form action="{{ route('sr.return.create') }}" method="GET" class="mb-4">
            <label class="form-label fw-bold">1. Select Order</label>
            <div class="input-group">
                <select name="order_id" class="input-form" onchange="this.form.submit()">
                    <option value="">-- Choose Delivered Order --</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                            BRS{{ $order->id }} - {{ $order->customer->shop_name }} ({{ $order->created_at->format('d M Y') }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-smart btn-blue">Load Items</button>
            </div>
        </form>

        @if($selectedOrder)
            <form action="{{ route('sr.return.store') }}" method="POST">
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
</script>
@endpush
