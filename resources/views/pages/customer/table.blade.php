@forelse($customers as $customer)
<tr>
  {{-- <td>{{ $user->id }}</td> --}}
  <td scope="row">{{ $loop->iteration }}</td>
  <td>BRC200{{ $customer->id }}</td>
  <td class="name">{{ $customer->shop_name }}</td>
  <td>{{ $customer->manager }}</td>
  <td>{{ $customer->address }}</td>

  <td class="action-icons">
    <a href="{{ route('customers.show', $customer) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center text-muted">No records found.</td>
</tr>
@endforelse