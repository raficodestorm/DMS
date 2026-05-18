@forelse($products as $product)
<div class="manage-card">
    <div class="card-body">
        <div><span>S.No</span>
            <p>{{ $products->firstItem() ? $products->firstItem() + $loop->index : $loop->iteration }}</p>
        </div>
        <div><span>Name</span>
            <p>{{ $product->name }}</p>
        </div>
        <div><span>SKU</span>
            <p>{{ $product->sku }}</p>
        </div>
        <div><span>Price</span>
            <p>{{ number_format($product->price, 2) }} TK</p>
        </div>
        <div><span>Status</span>
            <p>
                @if($product->status == 1)
                <span style="color:green;">● Active</span>
                @else
                <span style="color:red;">● Inactive</span>
                @endif
            </p>
        </div>
    </div>

    <div class="card-actions">
        <a href="{{ route('admin.products.show', $product) }}" class="icon-btn view-icon">
            <i class="fa-solid fa-eye"></i>
        </a>
    </div>
</div>
@empty
<p class="text-center text-muted">No records found.</p>
@endforelse
