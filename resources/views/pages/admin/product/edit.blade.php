@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Product</h2>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.products.update', $product->id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="input-box">
                <label>Name</label>
                <input class="input-form" name="name" value="{{ old('name', $product->name) }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>SKU</label>
                <input class="input-form" name="sku" value="{{ old('sku', $product->sku) }}" required>
                @error('sku')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Category</label>
                <select class="input-form" name="category_id">
                    <option value="">--Select Category--</option>
                    @foreach($categories as $category)
                    <option value="{{$category->id}}" {{ old('category_id', $product->category_id) == $category->id ?
                        'selected' :
                        '' }}>{{$category->name}}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Supplier</label>
                <select class="input-form" name="supplier_id">
                    <option value="">--Select Supplier--</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{$supplier->id}}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ?
                        'selected' :
                        '' }}>{{$supplier->company_name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-box">
                <label>Price</label>
                <input class="input-form" name="price" value="{{ old('price', $product->price) }}" required>
                @error('price')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Stock Alert Quantity</label>
                <input class="input-form" name="stock_alert" value="{{ old('stock_alert', $product->stock_alert) }}"
                    required>
                @error('stock_alert')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Description</label>
                <input class="input-form" name="description" value="{{ old('description', $product->description) }}"
                    required>
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

                        @if($product->image)
                        <img id="photoPreview" src="{{ asset('storage/' . $product->image) }}" alt="Preview"
                            style="display:block;">
                        <i class="fa-solid fa-user" id="defaultIcon" style="display:none;"></i>
                        @else
                        <i class="fa-solid fa-user" id="defaultIcon"></i>
                        <img id="photoPreview" src="" alt="Preview" style="display:none;">
                        @endif

                    </div>
                </div>
            </div>

            <div class="input-box">
                <label>Status</label>
                <select class="input-form" name="status">
                    <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>active</option>
                    <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>inactive</option>
                </select>
            </div>


            <div>
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary px-4 mt-5">Back</a>
    </div>
</div>
@endsection