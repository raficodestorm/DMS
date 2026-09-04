@forelse($bonuses as $bonus)
<tr>
    <td>{{ $bonus->bonus_date ? $bonus->bonus_date->format('d M Y') : 'N/A' }}</td>
    <td class="fw-bold">{{ $bonus->title }}</td>
    <td>
        @php
            $badgeClass = match($bonus->type) {
                'incentive' => 'bg-info',
                'cashback'  => 'bg-success',
                'special'   => 'bg-warning',
                default     => 'bg-secondary',
            };
        @endphp
        <span class="badge {{ $badgeClass }}">{{ ucfirst($bonus->type) }}</span>
    </td>
    <td class="fw-bold text-success">{{ number_format($bonus->amount, 2) }} ৳</td>
    <td>{{ $bonus->creator->username ?? 'N/A' }}</td>
    <td class="action-icons">
        <a href="{{ route('admin.bonuses.show', $bonus->id) }}" class="icon-btn view-icon" title="View">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.bonuses.edit', $bonus->id) }}" class="icon-btn edit-icon" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('admin.bonuses.destroy', $bonus->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this bonus entry?')">
            @csrf @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" title="Delete" style="border:none; background:none; cursor:pointer;">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted">No bonus entries found.</td>
</tr>
@endforelse
