@forelse($products as $product)
<tr>
    <td scope="row">{{ $products->firstItem() ? $products->firstItem() + $loop->index : $loop->iteration }}</td>
    <td class="name">{{ $product->name }}</td>
    <td>{{ $product->sku }}</td>
    <td>{{ number_format($product->price, 2) }} TK</td>
    <td>
        @if($product->status == 1)
        <span class="status-active-badge">● Active</span>
        @else
        <span class="status-inactive-badge">● Inactive</span>
        @endif
    </td>

    <td class="action-icons">
        <a href="{{ route('admin.products.show', $product) }}" class="icon-btn view-icon">
            <i class="fa-solid fa-eye"></i>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted">No records found.</td>
</tr>
@endforelse
