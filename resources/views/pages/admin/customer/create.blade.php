@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add New Customer</h2>
        @include('components.alert')

        <form class="adduser-form" method="POST" action="{{ route('admin.customers.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
  
            <div class="col-md-6">
                <label>Shop name</label>
                <input type="text" class="input-form" name="shop_name" required>
                @error('shop_name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Manager</label>
                <input type="text" class="input-form" name="manager" required>
                @error('manager')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Phone</label>
                <input type="text" class="input-form" name="phone" required>
                @error('phone')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label>Area Branch</label>
                <select name="branch_id" class="input-form" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-12">
                <label>Address</label>
                <textarea class="input-form" name="address"></textarea>
                @error('address')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            </div> <!-- end row  -->

            <div>
                <button class="btn-submit" type="submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection