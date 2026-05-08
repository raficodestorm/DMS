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
                <h2 class="mb-0">Return Request BRET{{ $return->id }}</h2>
                <p class="text-muted">Order Ref: BRS{{ $return->order_id }} | Date: {{ $return->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                @if($return->status == 'pending_manager')
                    <span class="badge bg-warning text-dark status-badge">Pending Manager</span>
                @elseif($return->status == 'pending_admin')
                    <span class="badge bg-info text-white status-badge">Pending Admin</span>
                @elseif($return->status == 'approved')
                    <span class="badge bg-success status-badge">Approved</span>
                @else
                    <span class="badge bg-danger status-badge">Rejected</span>
                @endif
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">Customer Details</h5>
                <p class="mb-1"><strong>{{ $return->customer->shop_name }}</strong></p>
                <p class="mb-1">{{ $return->customer->address }}</p>
                <p class="mb-1">Phone: {{ $return->customer->phone }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="fw-bold mb-3">Return Reason</h5>
                <p class="text-muted italic">{{ $return->reason ?? 'No reason provided' }}</p>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Return Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->price, 2) }} ৳</td>
                        <td class="text-end">{{ number_format($item->subtotal, 2) }} ৳</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total Return Amount</th>
                        <th class="text-end text-primary">{{ number_format($return->total_amount, 2) }} ৳</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-5">
            <a href="{{ route('sr.return.index') }}" class="btn-smart btn-blue">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
            @if($return->status == 'pending_manager')
                <a href="{{ route('sr.return.edit', $return->id) }}" class="btn-smart btn-green">
                    <i class="fas fa-edit me-1"></i> Edit Request
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
