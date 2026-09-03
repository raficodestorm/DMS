@forelse($customers as $customer)
<tr>
  <td scope="row">{{ $customers->firstItem() ? $customers->firstItem() + $loop->index : $loop->iteration }}</td>
  <td class="name">{{ $customer->fullname }}</td>
  <td>{{ $customer->username }}</td>
  <td>{{ $customer->customer_id ? 'BRC200' . $customer->customer_id : '--' }}</td>

  <td class="action-icons">
    <a href="{{ route('sr.users.show', $customer) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted">No records found.🚫</td>
</tr>
@endforelse
