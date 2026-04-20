@extends('layouts.adminlayout')

@section('content')
<p class="show-head">Employee details</p>
<div class="show-card">
    <div class="header-accent">
        <div class="photo-container">
            <img class="img-fluid"
                src="{{ $employee->photo ? asset('storage/' . $employee->photo) : 'https://ui-avatars.com/api/?name='.urlencode($employee->name).'&background=3131ff&color=fff' }}">
        </div>
    </div>

    <div class="content-area">
        <h1 class="show-name">{{ $employee->name }}</h1>
        <div class="rank-pill">{{ $employee->rank }}</div>

        <div class="info-list">
            <div class="info-group">
                <span class="i-label">Employee ID</span>
                <span class="i-value">BRE100{{ $employee->id }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Branch</span>
                <span class="i-value">{{ $employee->branch->name ?? 'Head Office' }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Father</span>
                <span class="i-value">{{ $employee->father }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Phone</span>
                <span class="i-value">{{ $employee->phone }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Email</span>
                <span class="i-value">{{ $employee->email }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Address</span>
                <span class="i-value">{{ $employee->address }}</span>
            </div>
            <div class="info-group">
                <span class="i-label">Created at</span>
                <span class="i-value">{{ $employee->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i
                    A') }}</span>
            </div>

        </div>
        <div class="statement">
            <p class="statement-text">
                "This pass certifies that <strong>{{ $employee->name }}</strong> is a verified professional of
                <strong>{{ config('app.name') }}</strong>.
                Dedicated to high-performance engineering and operational excellence,
                this team member plays a vital role in our mission to lead the industry."
            </p>
        </div>
    </div>

    <div class="card-footer-actions">

        <a href="{{ route('admin.employees.edit', $employee) }}" class="icon-btn edit-icon">
            <i class="fa-solid fa-pen"></i>
        </a>

        <a href="{{ route('admin.employees.qrcode', $employee) }}" class="icon-btn qr-icon" title="Generate QR Code">
            <i class="fa-solid fa-qrcode"></i>
        </a>

        <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="icon-btn delete-icon" style="border: none;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

</div>
<a href="{{ route('admin.employees.index') }}" class="back-btn">
    ← Back
</a>

@endsection