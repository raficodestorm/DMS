@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Add Category</h2>

        <form class="adduser-form" method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <div class="input-box">
                <label>Category name</label>
                <input class="input-form" name="name" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Description</label>
                <input class="input-form" name="description" required>
                @error('description')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <button class="btn-submit" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection