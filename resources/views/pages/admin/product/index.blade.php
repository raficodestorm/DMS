@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>All Products</h2>
        <p>Manage all registered products</p>
        @include('components.alert')
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="desktop-table">
                @forelse($products as $product)
                <tr>
                    {{-- <td>{{ $user->id }}</td> --}}
                    <td scope="row">{{ $products->firstItem() ? $products->firstItem() + $loop->index :
                        $loop->iteration
                        }}</td>
                    <td class="name">{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->price }}</td>
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
                    <td colspan="8" class="text-center text-muted">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="manage-mobile-cards">
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
                    <p>{{ $product->price }}</p>
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
    </div>


</div>
<div class="d-flex justify-content-center mt-3">
    {{ $products->links() }}
</div>

@endsection