@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add Supplier</h2>

        <form class="adduser-form" method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf

            <div class="input-box">
                <label>Name</label>
                <input class="input-form" name="name" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Company Name</label>
                <input class="input-form" name="company_name" required>
                @error('company_name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Phone</label>
                <input class="input-form" name="phone" required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Email</label>
                <input class="input-form" name="email" required>
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Address</label>
                <input class="input-form" name="address" required>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <button class="btn-submit" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection