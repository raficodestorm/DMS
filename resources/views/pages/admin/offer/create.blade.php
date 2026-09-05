@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add New Offer</h2>

        {{-- Success/Error Alert Component --}}
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.offers.store') }}">
            @csrf
            <div class="row">
            {{-- Offer Name --}}
            <div class="col-md-6">
                <label>Offer Name</label>
                <input class="input-form" name="name" placeholder="e.g. Eid Dhamaka" required value="{{ old('name') }}">
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- Product Selection --}}
            <div class="col-md-6">
                <label>Select Product</label>
                <select class="input-form" name="product_id" required>
                    <option value="">--Select Product--</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id')==$product->id ? 'selected' : '' }}>
                        {{ $product->name }} (SKU: {{ $product->sku }})
                    </option>
                    @endforeach
                </select>
                @error('product_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- Offer Type (Percentage or Fixed) --}}
            <div class="col-md-6">
                <label>Offer Type</label>
                <select class="input-form" name="type" required>
                    <option value="percentage" {{ old('type')=='percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ old('type')=='fixed' ? 'selected' : '' }}>Fixed Amount (TK)</option>
                </select>
                @error('type')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- Discount Amount --}}
            <div class="col-md-6">
                <label>Discount Amount</label>
                <input type="number" step="0.01" class="input-form" name="discount_amount" placeholder="0.00" required
                    value="{{ old('discount_amount') }}">
                @error('discount_amount')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- Date Range - Grid use kora hoyeche side-by-side thakar jonno --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-box">
                    <label>Start Date</label>
                    <input type="date" class="input-form" name="start_date" required value="{{ old('start_date') }}">
                    @error('start_date')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="input-box">
                    <label>End Date</label>
                    <input type="date" class="input-form" name="end_date" required value="{{ old('end_date') }}">
                    @error('end_date')<div class="error-text">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <label>Status</label>
                <select class="input-form" name="status">
                    <option value="1" {{ old('status')=='1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status')=='0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<div class="error-text">{{ $message }}</div>@enderror
            </div>

</div>

            <div class="mt-4">
                <button class="btn-submit" type="submit">Save Offer</button>
            </div>
        </form>
    </div>
</div>
@endsection