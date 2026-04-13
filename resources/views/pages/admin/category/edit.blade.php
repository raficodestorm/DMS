@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Category</h2>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form class="adduser-form" method="POST" action="{{ route('admin.categories.update', $category->id) }}">
            @csrf
            @method('PUT')
            <div class="input-box">
                <label>Category name</label>
                <input class="input-form" name="name" value="{{ $category->name }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Description</label>
                <input class="input-form" name="description" value="{{ $category->description }}" required>
                @error('description')<div class="error-text">{{ $message }}</div>@enderror
            </div>


            <div>
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary px-4 mt-5">Back</a>
    </div>
</div>
@endsection