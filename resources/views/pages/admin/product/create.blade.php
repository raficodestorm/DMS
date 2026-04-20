@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add New Product</h2>
        @include('components.alert')
        <form class="adduser-form" method="POST" action="{{ route('admin.products.store') }}"
            enctype="multipart/form-data">
            @csrf

            <div class="input-box">
                <label>Name</label>
                <input class="input-form" name="name" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>SKU</label>
                <input class="input-form" name="sku" required>
                @error('sku')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Category</label>
                <select class="input-form" name="category_id">
                    <option value="">--Select Category--</option>
                    @foreach($categories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Supplier</label>
                <select class="input-form" name="supplier_id">
                    <option value="">--Select Supplier--</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{$supplier->id}}">{{$supplier->company_name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-box">
                <label>Price</label>
                <input class="input-form" name="price" required>
                @error('price')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Stock Alert Quantity</label>
                <input class="input-form" name="stock_alert" required>
                @error('stock_alert')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Description</label>
                <input class="input-form" name="description" required>
                @error('description')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="photo-upload">
                <div class="upload-left">
                    <label>Product Image</label>
                    <input class="input-form" type="file" name="image" id="photoInput">
                    @error('image')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="upload-right">
                    <div class="preview-box">
                        <i class="fa-solid fa-user" id="defaultIcon"></i>
                        <img id="photoPreview" src="" alt="Preview">
                    </div>
                </div>
            </div>

            <div>
                <button class="btn-submit" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('photoInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const preview = document.getElementById('photoPreview');
    const icon = document.getElementById('defaultIcon');

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            icon.style.display = 'none';
        }

        reader.readAsDataURL(file);
    }
});
</script>
@endpush