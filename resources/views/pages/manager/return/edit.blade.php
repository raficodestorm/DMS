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
            <h2 class="m-0"><i class="fas fa-edit text-primary"></i> Edit Return Request BRET{{ $return->id }}</h2>
            <a href="{{ route('manager.return.index') }}" class="btn-smart btn-blue">
                <i class="fas fa-list me-1"></i> Back to List
            </a>
        </div>

        @include('components.alert')

        <form action="{{ route('manager.return.update', $return->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4 p-3 bg-light rounded border">
                <strong>Order: BRS{{ $return->order_id }}</strong><br>
                <small class="text-muted">Customer: {{ $return->customer->shop_name ?? 'Retail/Not Found'}}</p>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Select Items to Return</label>
                <div id="items-wrapper">
                    @foreach($return->order->items as $item)
                        @php
                            // Calculate available qty considering other returns
                            $otherReturnsQty = \App\Models\ReturnItem::whereHas('productReturn', function($q) use ($return) {
                                $q->where('order_id', $return->order_id)
                                  ->where('status', '!=', 'rejected')
                                  ->where('id', '!=', $return->id);
                            })->where('product_id', $item->product_id)->sum('quantity');

                            $availableToReturn = $item->quantity - $otherReturnsQty;
                            $currentReturnQty = $return->items->where('product_id', $item->product_id)->first()->quantity ?? 0;
                        @endphp

                        @if($availableToReturn > 0)
                            <div class="order-item-row">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <strong>{{ $item->product->name }}</strong><br>
                                        <small class="text-muted">Purchased: {{ $item->quantity }} | Price: {{ number_format($item->selling_rate, 2) }} ৳</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted d-block">Available to Return: {{ $availableToReturn }}</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   name="items[{{ $loop->index }}][quantity]" 
                                                   class="form-control return-qty" 
                                                   data-price="{{ $item->selling_rate }}"
                                                   min="0" 
                                                   max="{{ $availableToReturn }}" 
                                                   value="{{ $currentReturnQty }}"
                                                   oninput="calculateTotals()">
                                            <span class="input-group-text">Qty</span>
                                        </div>
                                        <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <label class="small text-muted d-block">Subtotal</label>
                                        <span class="item-subtotal">{{ number_format($currentReturnQty * $item->selling_rate, 2) }}</span> ৳
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Reason for Return</label>
                <textarea name="reason" class="input-form" rows="3">{{ old('reason', $return->reason) }}</textarea>
            </div>

            <div class="p-summary-card mb-4" style="background: var(--background); padding: 15px; border-radius: 10px; border: 1px solid var(--border-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-0 text-muted">Total Return Amount</p>
                        <h3 class="mb-0 text-primary" id="total-display">{{ number_format($return->total_amount, 2) }} ৳</h3>
                    </div>
                    
                </div>
            </div>
            <button type="submit" class="btn-smart btn-green py-2 px-4">
                        Update Return Request <i class="fas fa-check-circle ms-1"></i>
                    </button>
        </form>
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
            if (row) {
                if (qty > 0) {
                    row.classList.add('selected');
                } else {
                    row.classList.remove('selected');
                }
            }

            input.closest('.row').querySelector('.item-subtotal').innerText = subtotal.toFixed(2);
            total += subtotal;
        });
        document.getElementById('total-display').innerText = total.toFixed(2) + ' ৳';
    }
    // Run on load to ensure initial totals are correct
    window.onload = calculateTotals;
</script>
@endpush
