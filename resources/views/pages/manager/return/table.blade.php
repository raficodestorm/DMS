@php $sl = ($returns->currentPage() - 1) * $returns->perPage() + 1; @endphp
@foreach($returns as $return)
<tr>
    <td>{{ $sl++ }}</td>
    <td>BRET{{ $return->id }}</td>
    <td>{{ $return->sr->username }}</td>
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
            <a href="{{ route('manager.return.show', $return->id) }}" class="btn-sm-smart btn-blue" title="View">
                <i class="fas fa-eye"></i>
            </a>
            @if($return->status == 'pending_manager')
                <form action="{{ route('manager.return.forward', $return->id) }}" method="POST" onsubmit="return confirm('Forward this return to Admin for approval?')">
                    @csrf
                    <button type="submit" class="btn-sm-smart btn-green" title="Forward to Admin">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
                <form action="{{ route('manager.return.reject', $return->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this return?')">
                    @csrf
                    <button type="submit" class="btn-sm-smart btn-red" title="Reject">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
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
