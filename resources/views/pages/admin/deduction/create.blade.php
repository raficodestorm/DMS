@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add Your Deduction</h2>
        <p style="color: gray;">Always input decimal number and it will count as percentage in the software system.</p>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.deductions.store') }}">
            @csrf
            <div class="row">
            <div class="col-md-6">
                <label>Supplier</label>
                <select name="supplier_id" class="input-form" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            
            <div class="col-md-6">
                <label>Deduction Type</label>
                <select class="input-form" name="type" required>
                    <option value="main" {{ old('type')=='main' ? 'selected' : '' }}>Main (%)</option>

                    {{-- <option value="specific" {{ old('type')=='specific' ? 'selected' : '' }}>Specific (%)</option>
                    <option value="console" {{ old('type')=='console' ? 'selected' : '' }}>Console (%)</option> --}}

                </select>
                @error('type')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Customer Deduction</label>
                <input type="number" step="0.01" class="input-form" name="customer_deduction" placeholder="0.00"
                    required value="{{ old('customer_deduction') }}">
                @error('customer_deduction')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Retail Deduction</label>
                <input type="number" step="0.01" class="input-form" name="retail_deduction" placeholder="0.00"
                    required value="{{ old('retail_deduction') }}">
                @error('retail_deduction')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Own Deduction</label>
                <input type="number" step="0.01" class="input-form" name="my_deduction" placeholder="0.00" required
                    value="{{ old('my_deduction') }}">
                @error('my_deduction')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            </div>

            
            <div class="mt-4">
                <button class="btn-submit" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection