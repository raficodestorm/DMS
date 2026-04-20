@extends('layouts.adminlayout')

@section('content')
<p class="show-head">Branch details</p>
<div class="show-card">
    <div class="header-accent">
    </div>

    <div class="content-area">
        <div class="rank-pill">Details of {{ $branch->name }} Branch</div>
        <div class="info-list">
            <div class="info-group">
                <span class="i-label">Branch Name</span>
                <span class="i-value">{{ $branch->name }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">manager</span>
                <span class="i-value">{{ $branch->manager }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Address</span>
                <span class="i-value">{{ $branch->address }}</span>
            </div>

            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $branch->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A')
                    }}</span>
            </div>

        </div>
        <div class="statement">
            <p class="statement-text">
                "<strong>{{ $branch->name }}</strong> is a verified branch of
                <strong>{{ config('app.name') }}</strong>.
                This branch plays a vital role in our mission to lead the industry."
            </p>
        </div>
    </div>

    <div class="card-footer-actions">

        <a href="{{ route('admin.branches.edit', $branch) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen"></i>
        </a>

        <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>
<a href="{{ route('admin.branches.index') }}" class="back-btn">
    ← Back
</a>

@endsection