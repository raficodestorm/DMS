@forelse($returns as $return)
<tr>
    <td>{{ $returns->firstItem() ? $returns->firstItem() + $loop->index : $loop->iteration }}</td>
    <td>BRET{{ $return->id }}</td>
    <td>{{ $return->sr->username ?? 'N/A' }}</td>
    <td>{{ $return->customer->shop_name ?? 'Retail/Not found' }}</td>
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
    <td>{{ $return->created_at->format('d M Y') }}</td>
    <td class="action-icons">
        <a href="{{ route('manager.return.show', $return->id) }}" class="icon-btn view-icon" title="View">
            <i class="fa-solid fa-eye"></i>
        </a>
       @if($return->status == 'pending_manager' || $return->status == 'pending_sr')
        <a href="{{ route('manager.return.edit', $return->id) }}" class="icon-btn edit-icon" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
    @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-muted">No return requests found.</td>
</tr>
@endforelse
