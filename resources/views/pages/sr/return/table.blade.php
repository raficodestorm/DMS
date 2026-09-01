@forelse($returns as $return)
<tr>
    <td>{{ $returns->firstItem() ? $returns->firstItem() + $loop->index : $loop->iteration }}</td>
    <td>BRET{{ $return->id }}</td>
    <td>BRS{{ $return->order_id }}</td>
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
    <td>{{ $return->created_at->format('d M Y') }}</td>
    <td class="action-icons">
        <a href="{{ route('sr.return.show', $return->id) }}" class="icon-btn view-icon" title="View">
            <i class="fa-solid fa-eye"></i>
        </a>
        @if($return->status == 'pending_sr')
            <a href="{{ route('sr.return.edit', $return->id) }}" class="icon-btn edit-icon" title="Edit">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>

            <form action="{{ route('sr.return.destroy', $return->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="icon-btn delete-icon" onclick="return confirm('Delete this record permanently?')" style="border: none;">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-muted">No return requests found.</td>
</tr>
@endforelse
