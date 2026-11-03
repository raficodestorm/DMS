@forelse($customers as $customer)
<tr>
  <td scope="row">{{ $customers->firstItem() ? $customers->firstItem() + $loop->index : $loop->iteration }}</td>
  <td>BRC200{{ $customer->id }}</td>
  <td class="name">{{ $customer->shop_name }}</td>
  <td>
    @if($customer->customer_type === 'retail')
      <span class="purple-type-badge">Retail</span>
    @else
      <span class="emerald-type-badge">Wholesale</span>
    @endif
  </td>
  <td>{{ $customer->address }}</td>
  <td>{{ $customer->branch->name ?? 'N/A' }}</td>

  <td class="action-icons">
    <a href="{{ route('admin.customers.show', $customer) }}" class="icon-btn view-icon">
      <i class="fa-solid fa-eye"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted">No records found.</td>
</tr>
@endforelse