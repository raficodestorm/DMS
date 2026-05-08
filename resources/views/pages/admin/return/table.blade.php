@php $sl = ($returns->currentPage() - 1) * $returns->perPage() + 1; @endphp
@foreach($returns as $return)
<tr>
    <td>{{ $sl++ }}</td>
    <td>BRET{{ $return->id }}</td>
    <td>{{ $return->branch->name ?? 'N/A' }}</td>
    <td>{{ $return->sr->username }}</td>
    <td>{{ $return->customer->shop_name }}</td>
    <td>{{ number_format($return->total_amount, 2) }} ৳</td>
    <td>
        @if($return->status == 'pending_manager')
            <span class="badge bg-warning">Pending Manager</span>
        @elseif($return->status == 'pending_admin')
            <span class="badge bg-info text-white">Pending Approval</span>
        @elseif($return->status == 'approved')
            <span class="badge bg-success">Approved</span>
        @else
            <span class="badge bg-danger">Rejected</span>
        @endif
    </td>
    <td>
        <div style="display: flex; gap: 5px;">
            <a href="{{ route('admin.return.show', $return->id) }}" class="btn-sm-smart btn-blue" title="View">
                <i class="fas fa-eye"></i>
            </a>
            @if($return->status == 'pending_admin')
                <form action="{{ route('admin.return.approve', $return->id) }}" method="POST" onsubmit="return confirm('APPROVE this return? This will update stocks, orders, and customer balances.')">
                    @csrf
                    <button type="submit" class="btn-sm-smart btn-green" title="Approve">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.return.destroy', $return->id) }}" method="POST" onsubmit="return confirm('DELETE this return? If approved, all changes will be rolled back.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-sm-smart btn-red" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@endforeach

@if($returns->isEmpty())
<tr>
    <td colspan="8" style="text-align: center; padding: 20px;">No return requests found.</td>
</tr>
@endif
