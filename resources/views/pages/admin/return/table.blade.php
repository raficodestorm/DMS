@forelse($returns as $return)
<tr>
    <td>{{ $returns->firstItem() ? $returns->firstItem() + $loop->index : $loop->iteration }}</td>
    <td>BRET{{ $return->id }}</td>
    <td>{{ $return->branch->name ?? 'N/A' }}</td>
    <td>{{ $return->sr->username ?? 'N/A' }}</td>
    <td>{{ $return->customer->shop_name ?? 'N/A' }}</td>
    <td>{{ number_format($return->total_amount, 2) }} ৳</td>
    <td>
        @if($return->status == 'pending_sr')
            <span class="status-pending-badge">Pending SR</span>
        @elseif($return->status == 'pending_manager')
            <span class="status-pmanager-badge">Pending For Approval</span>
        @elseif($return->status == 'approved')
            <span class="status-approved-badge">Approved</span>
        @else
            <span class="status-rejected-badge">Rejected</span>
        @endif
    </td>
    <td class="action-icons">
        <a href="{{ route('admin.return.show', $return->id) }}" class="icon-btn view-icon" title="View">
            <i class="fa-solid fa-eye"></i>
        </a>
        
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-muted">No return requests found.</td>
</tr>
@endforelse
