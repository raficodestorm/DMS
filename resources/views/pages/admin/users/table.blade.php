@forelse($users as $user)
<tr>
  <td scope="row">{{ $users->firstItem() ? $users->firstItem() + $loop->index : $loop->iteration }}</td>
  <td class="name">{{ $user->fullname ?? "-" }}</td>
  <td>{{ $user->username ?? "-" }}</td>
  <td><span class="badge bg-primary text-uppercase">{{ $user->role ?? "-" }}</span></td>
  <td>{{ $user->branch->name ?? "-" }}</td>

  <td class="action-icons">
    <a href="{{ route('admin.users.show', $user) }}" class="icon-btn view-icon" title="View">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted">No records found.</td>
</tr>
@endforelse
