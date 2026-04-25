@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Offer: {{ $offer->name }}</h2>

        {{-- Success/Error Alert Component --}}
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.offers.update', $offer->id) }}">
            @csrf
            @method('PUT') {{-- Senior Note: Update action er jonno PUT method dorkar --}}

            {{-- Offer Name --}}
            <div class="input-box">
                <label>Offer Name</label>
                <input class="input-form" name="name" value="{{ old('name', $offer->name) }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- Product Selection --}}
            <div>
                <label>Select Product</label>
                <select class="input-form" name="product_id" required>
                    <option value="">--Select Product--</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ (old('product_id', $offer->product_id) == $product->id) ?
                        'selected' : '' }}>
                        {{ $product->name }} (SKU: {{ $product->sku }})
                    </option>
                    @endforeach
                </select>
                @error('product_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- Offer Type --}}
            <div>
                <label>Offer Type</label>
                <select class="input-form" name="type" required>
                    <option value="percentage" {{ (old('type', $offer->type) == 'percentage') ? 'selected' : ''
                        }}>Percentage (%)</option>
                    <option value="fixed" {{ (old('type', $offer->type) == 'fixed') ? 'selected' : '' }}>Fixed Amount
                        (TK)</option>
                </select>
                @error('type')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- Discount Amount --}}
            <div class="input-box">
                <label>Discount Amount</label>
                <input type="number" step="0.01" class="input-form" name="discount_amount"
                    value="{{ old('discount_amount', $offer->discount_amount) }}" required>
                @error('discount_amount')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            {{-- Date Range Grid --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-box">
                    <label>Start Date</label>
                    <input type="date" class="input-form" name="start_date"
                        value="{{ old('start_date', $offer->start_date) }}" required>
                    @error('start_date')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="input-box">
                    <label>End Date</label>
                    <input type="date" class="input-form" name="end_date"
                        value="{{ old('end_date', $offer->end_date) }}" required>
                    @error('end_date')<div class="error-text">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label>Status</label>
                <select class="input-form" name="status">
                    <option value="1" {{ (old('status', $offer->status) == '1') ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ (old('status', $offer->status) == '0') ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="mt-4">
                <button class="btn-submit" type="submit">Update Offer</button>
            </div>
        </form>
    </div>
</div>
@endsection