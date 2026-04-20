@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Supplier</h2>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.suppliers.update', $supplier->id) }}">
            @csrf
            @method('PUT')

            <div class="input-box">
                <label>Name</label>
                <input class="input-form" name="name" value="{{ old('name', $supplier->name) }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Company Name</label>
                <input class="input-form" name="company_name" value="{{ old('company_name', $supplier->company_name) }}"
                    required>
                @error('company_name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Phone</label>
                <input class="input-form" name="phone" value="{{ old('phone', $supplier->phone) }}" required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Email</label>
                <input class="input-form" name="email" value="{{ old('email', $supplier->email) }}" required>
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Address</label>
                <input class="input-form" name="address" value="{{ old('address', $supplier->address) }}" required>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>


            <div>
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary px-4 mt-5">Back</a>
    </div>
</div>
@endsection