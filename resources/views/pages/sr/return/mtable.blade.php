@foreach($returns as $return)
<div class="mobile-card">
    <div class="mobile-card-header">
        <strong>BRET{{ $return->id }}</strong>
        <span>{{ $return->created_at->format('d M Y') }}</span>
    </div>
    <div class="mobile-card-body">
        <p><strong>Customer:</strong> {{ $return->customer->shop_name }}</p>
        <p><strong>Amount:</strong> {{ number_format($return->total_amount, 2) }} ৳</p>
        <p><strong>Status:</strong> 
            @if($return->status == 'pending_manager')
                <span class="badge bg-warning">Pending Manager</span>
            @elseif($return->status == 'pending_admin')
                <span class="badge bg-info text-white">Pending Admin Approval</span>
            @elseif($return->status == 'approved')
                <span class="badge bg-success">Approved</span>
            @else
                <span class="badge bg-danger">Rejected</span>
            @endif
        </p>
    </div>
    <div class="mobile-card-actions">
        <a href="{{ route('sr.return.show', $return->id) }}" class="btn-sm-smart btn-blue">View</a>
        @if($return->status == 'pending_manager')
            <a href="{{ route('sr.return.edit', $return->id) }}" class="btn-sm-smart btn-green">Edit</a>
        @endif
    </div>
</div>
@endforeach
