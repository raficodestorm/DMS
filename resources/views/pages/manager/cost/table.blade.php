@forelse($costs as $cost)
<tr>
    <td>{{ $costs->firstItem() ? $costs->firstItem() + $loop->index : $loop->iteration }}</td>
    <td>{{ $cost->cost_date ? $cost->cost_date->format('d M Y') : 'N/A' }}</td>
    <td>
        <span class="badge bg-secondary">{{ ucfirst($cost->category) }}</span>
    </td>
    <td>{{ Str::limit($cost->description, 40) }}</td>
    <td><strong>{{ number_format($cost->amount, 2) }} TK</strong></td>
    <td>{{ $cost->creator->username ?? 'N/A' }}</td>
    <td class="action-icons">
        <a href="{{ route('manager.costs.show', $cost->id) }}" class="icon-btn view-icon" title="View">
            <i class="fa-solid fa-eye"></i>
        </a>
        <a href="{{ route('manager.costs.edit', $cost->id) }}" class="icon-btn edit-icon" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('manager.costs.destroy', $cost->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this cost record?')">
            @csrf @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" title="Delete" style="border:none; background:none; padding:0; cursor:pointer;">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">No expense records found.</td>
</tr>
@endforelse
