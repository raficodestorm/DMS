@forelse($stockCuts as $cut)
<tr>
  <td>{{ $stockCuts->firstItem() ? $stockCuts->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>{{ $cut->created_at ? $cut->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
  <td>{{ $cut->supplier->company_name ?? 'N/A' }}</td>
  <td>{{ $cut->requestedBy->fullname ?? $cut->requestedBy->username ?? 'N/A' }}</td>
  <td>{{ number_format($cut->net_total, 2) }} TK</td>
  <td class="action-icons">
    <div style="display: flex; gap: 5px;">
      <a href="{{ route('admin.stock.cut.cut.show', $cut->id) }}" class="icon-btn view-icon" title="View Detail">
        <i class="fas fa-eye"></i>
      </a>
      <a href="{{ route('admin.stock.cut.cut.edit', $cut->id) }}" class="icon-btn edit-icon" title="Edit Record">
        <i class="fas fa-edit"></i>
      </a>
      <form action="{{ route('admin.stock.cut.cut.destroy', $cut->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record? Stock will be restored.')" style="margin: 0;">
        @csrf
        @method('DELETE')
        <button type="submit" class="icon-btn delete-icon" style="border: none; cursor: pointer;" title="Delete Record">
          <i class="fas fa-trash"></i>
        </button>
      </form>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted">No stock cuts found.</td>
</tr>
@endforelse
