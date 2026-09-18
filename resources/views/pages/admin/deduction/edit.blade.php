@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit your deduction</h2>
        <p style="color: gray;">Always input decimal number and it will count as percentage in the software system.</p>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.deductions.update', $deduction->id) }}">
            @csrf
            @method('PUT')
            <div class="row">

                <div class="col-md-6">
                <label>Supplier</label>
                <select name="supplier_id" class="input-form" required>
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ (old('supplier_id', $deduction->supplier_id) == $supplier->id) ? 'selected' : '' }}>
                        {{ $supplier->company_name }}
                    </option>
                    @endforeach
                </select>
                @error('supplier_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Deduction Type</label>
                <select class="input-form" name="type" required>
                    <option value="main" {{ (old('type', $deduction->type)=='main') ? 'selected' : '' }}>Main (%)
                    </option>

                    {{-- <option value="specific" {{ (old('type', $deduction->type)=='specific') ? 'selected' : ''
                        }}>Specific (%)</option>
                    <option value="console" {{ (old('type', $deduction->type)=='console') ? 'selected' : '' }}>Console
                        (%)</option> --}}

                </select>
                @error('type')<div class="error-text">{{ $message }}</div>@enderror
            </div>


            <div class="col-md-6">
                <label>Customer Deduction</label>
                <input type="number" step="0.01" class="input-form" name="customer_deduction" placeholder="0.00"
                    required value="{{ old('customer_deduction', $deduction->customer_deduction) }}">
                @error('customer_deduction')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Retail Deduction</label>
                <input type="number" step="0.01" class="input-form" name="retail_deduction" placeholder="0.00"
                    required value="{{ old('retail_deduction', $deduction->retail_deduction) }}">
                @error('retail_deduction')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Own Deduction</label>
                <input type="number" step="0.01" class="input-form" name="my_deduction" placeholder="0.00" required
                    value="{{ old('my_deduction', $deduction->my_deduction) }}">
                @error('my_deduction')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            </div>


            <div class="mt-4">
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection