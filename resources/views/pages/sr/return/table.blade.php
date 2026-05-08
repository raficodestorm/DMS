@php $sl = ($returns->currentPage() - 1) * $returns->perPage() + 1; @endphp
@foreach($returns as $return)
<tr>
    <td>{{ $sl++ }}</td>
    <td>BRET{{ $return->id }}</td>
    <td>BRS{{ $return->order_id }}</td>
    <td>{{ $return->customer->shop_name }}</td>
    <td>{{ number_format($return->total_amount, 2) }} ৳</td>
    <td>
        @if($return->status == 'pending_manager')
            <span class="badge bg-warning">Pending Manager</span>
        @elseif($return->status == 'pending_admin')
            <span class="badge bg-info text-white">Pending Admin Approval</span>
        @elseif($return->status == 'approved')
            <span class="badge bg-success">Approved</span>
        @else
            <span class="badge bg-danger">Rejected</span>
        @endif
    </td>
    <td>{{ $return->created_at->format('d M Y') }}</td>
    <td>
        <div style="display: flex; gap: 5px;">
            <a href="{{ route('sr.return.show', $return->id) }}" class="btn-sm-smart btn-blue" title="View">
                <i class="fas fa-eye"></i>
            </a>
            @if($return->status == 'pending_manager')
                <a href="{{ route('sr.return.edit', $return->id) }}" class="btn-sm-smart btn-green" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
            @endif
        </div>
    </td>
</tr>
@endforeach

@if($returns->isEmpty())
<tr>
    <td colspan="8" style="text-align: center; padding: 20px;">No return requests found.</td>
</tr>
@endif
