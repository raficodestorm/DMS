@forelse($employees as $employee)
<tr>
  {{-- <td>{{ $user->id }}</td> --}}
  <td scope="row">{{ $loop->iteration }}</td>
  <td>BRE100{{ $employee->id }}</td>
  <td class="name">{{ $employee->name }}</td>
  <td>{{ $employee->rank }}</td>
  <td>{{ $employee->phone }}</td>
  <td>{{ $employee->branch->name }}</td>

  <td class="action-icons">
    <a href="{{ route('admin.employees.show', $employee) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center text-muted">No records found.</td>
</tr>
@endforelse