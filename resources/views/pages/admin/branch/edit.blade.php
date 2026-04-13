@extends('layouts.adminlayout')

@section('content')

<div class="container justify-center">
    <div class="form-card">
        <h2>Edit Branch</h2>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form class="adduser-form" method="POST" action="{{ route('admin.branches.update', $branch->id) }}">
            @csrf
            @method('PUT')
            <div class="input-box">
                <label>Branch name</label>
                <input class="input-form" name="name" value="{{ $branch->name }}" required>
                @error('name')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Manager name</label>
                <input class="input-form" name="manager" value="{{ $branch->manager }}" required>
                @error('manager')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="input-box">
                <label>Address</label>
                <input class="input-form" name="address" type="text" value="{{ $branch->address }}" required>
                @error('email')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div>
                <button class="btn-submit" type="submit">Update</button>
            </div>
        </form>
        <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary px-4 mt-5">Back</a>
    </div>
</div>
@endsection