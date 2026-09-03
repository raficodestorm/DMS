@forelse($srs as $sr)
<tr>
  <td scope="row">{{ $srs->firstItem() ? $srs->firstItem() + $loop->index : $loop->iteration }}</td>
  <td class="name">{{ $sr->fullname }}</td>
  <td>{{ $sr->username }}</td>
  <td>{{ $sr->branch->name ?? " " }}</td>

  <td class="action-icons">
    <a href="{{ route('manager.users.show', $sr) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted">No records found.</td>
</tr>
@endforelse
