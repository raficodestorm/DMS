@extends('layouts.adminlayout')

@section('content')
<p class="show-head">Category details</p>
<div class="show-card">
    <div class="header-accent">
    </div>

    <div class="content-area">
        <div class="rank-pill">Details of {{ $category->name }} category</div>
        <div class="info-list">
            <div class="info-group">
                <span class="i-label">Category Name</span>
                <span class="i-value">{{ $category->name }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Description</span>
                <span class="i-value">{{ $category->description }}</span>
            </div>

            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $category->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i
                    A') }}</span>
            </div>

        </div>
        <div class="statement">
            <p class="statement-text">
                "<strong>{{ $category->name }}</strong> is a verified category of
                <strong>R.Electric</strong>.
                This category plays a vital role in our mission to lead the industry."
            </p>
        </div>
    </div>

    <div class="card-footer-actions">

        <a href="{{ route('admin.categories.edit', $category) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>
<a href="{{ route('admin.categories.index') }}" class="back-btn">
    ← Back
</a>

@endsection