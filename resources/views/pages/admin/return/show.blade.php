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
                @if($return->status == 'pending_manager')
                    <span class="badge bg-warning text-dark status-badge">Pending Manager</span>
                @elseif($return->status == 'pending_admin')
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
                <p class="mb-1"><strong>{{ $return->customer->shop_name }}</strong></p>
                <p class="mb-1">{{ $return->customer->address }}</p>
                <p class="mb-1">Current Due: <strong>{{ number_format($return->customer->due, 2) }} ৳</strong></p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="fw-bold mb-3">Return Context</h5>
                <p class="mb-1">Original Order: <strong>BRS{{ $return->order_id }}</strong></p>
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
                <form action="{{ route('admin.return.destroy', $return->id) }}" method="POST" onsubmit="return confirm('DELETE this return request? All associated changes will be rolled back.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-smart btn-red">
                        <i class="fas fa-trash me-1"></i> Delete Permanent
                    </button>
                </form>
            </div>
            
            @if($return->status == 'pending_admin')
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
@endsection
