@extends('layouts.adminlayout')

@section('content')
<div class="manage-card">

    <div class="card-header">
        <h2>All Products</h2>
        <p>Manage all registered products</p>
    </div>

    <div style="margin: 15px 0; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <input type="text" id="search" class="input-form" placeholder="Search by Product Name...">
        <input type="number" id="amount" class="input-form" placeholder="Filter by Minimum Price...">
    </div>

    @include('components.alert')

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
            <tbody class="desktop-table" id="productTable">
                @include('pages.admin.product.table')
            </tbody>
        </table>
    </div>
    <div class="manage-mobile-cards" id="productMobile">
        @include('pages.admin.product.mtable')
    </div>


</div>
<div class="d-flex justify-content-center mt-3" id="pagination-links">
    {{ $products->links() }}
</div>

@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('search');
    const amountInput = document.getElementById('amount');
    const productTable = document.getElementById('productTable');
    const productMobile = document.getElementById('productMobile');

    const filterProducts = () => {
        let search = searchInput.value;
        let amount = amountInput.value;

        fetch(`{{ route('admin.products.index') }}?search=${search}&amount=${amount}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            productTable.innerHTML = data.table;
            productMobile.innerHTML = data.mobile;
        });
    };

    searchInput.addEventListener('keyup', filterProducts);
    amountInput.addEventListener('input', filterProducts);
</script>
@endpush