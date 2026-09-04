@forelse($employees as $employee)
<tr>
  <td scope="row">{{ $employees->firstItem() ? $employees->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>BRE100{{ $employee->id }}</td>
  <td class="name">{{ $employee->name }}</td>
  <td>{{ $employee->rank }}</td>
  <td>{{ $employee->phone }}</td>
  <td>{{ $employee->email }}</td>

  <td class="action-icons">
    <a href="{{ route('manager.employees.show', $employee) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted">No records found.</td>
</tr>
@endforelse