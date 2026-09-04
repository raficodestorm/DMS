@forelse($customers as $customer)
<tr>
  <td scope="row">{{ $customers->firstItem() ? $customers->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>BRC200{{ $customer->id }}</td>
  <td class="name">{{ $customer->shop_name }}</td>
  <td>{{ $customer->manager }}</td>
  <td>{{ number_format($customer->due ?? 0, 2) }} TK</td>

  <td class="action-icons">
    <a href="{{ route('customers.show', $customer) }}" class="icon-btn view-icon" title="View">
      <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('customers.edit', $customer) }}" class="icon-btn edit-icon" title="Edit">
      <i class="fas fa-edit"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted py-4">No customer records found.</td>
</tr>
@endforelse