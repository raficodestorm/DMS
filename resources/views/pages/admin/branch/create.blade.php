@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add Branch</h2>
        @include('components.alert')
        <form class="adduser-form" method="POST" action="{{ route('admin.branches.store') }}">
            @csrf

            <div class="input-box">
                <label>Branch name</label>
                <input class="input-form" name="name" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Manager name</label>
                <input class="input-form" name="manager" required>
                @error('manager')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Address</label>
                <input class="input-form" name="address" type="text" required>
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <button class="btn-submit" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection