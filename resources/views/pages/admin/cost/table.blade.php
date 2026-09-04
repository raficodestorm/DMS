@forelse($costs as $cost)
<tr>
    <td>{{ $cost->cost_date ? $cost->cost_date->format('d M Y') : 'N/A' }}</td>
    <td>
        <span class="badge bg-secondary">{{ ucfirst($cost->category) }}</span>
    </td>
    <td class="fw-bold">{{ Str::limit($cost->description, 40) }}</td>
    <td class="fw-bold text-danger">{{ number_format($cost->amount, 2) }} ৳</td>
    <td>{{ $cost->creator->username ?? 'N/A' }}</td>
    <td class="action-icons">
        <a href="{{ route('admin.company_costs.show', $cost->id) }}" class="icon-btn view-icon" title="View">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.company_costs.edit', $cost->id) }}" class="icon-btn edit-icon" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('admin.company_costs.destroy', $cost->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
            @csrf @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" title="Delete" style="border:none; background:none; cursor:pointer;">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted">No expense records found.</td>
</tr>
@endforelse
