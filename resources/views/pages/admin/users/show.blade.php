@extends('layouts.adminlayout')

@section('content')
<p class="show-head">User details</p>
<div class="show-card">
  <div class="header-accent">
    <div class="photo-container">
      <img class="img-fluid"
        src="{{ $user->profile_photo_path ? (str_starts_with($user->profile_photo_path, 'uploads/') ? asset($user->profile_photo_path) : asset('uploads/' . $user->profile_photo_path)) : 'https://ui-avatars.com/api/?name='.urlencode($user->username).'&background=3131ff&color=fff' }}">
    </div>
  </div>

  <div class="content-area">
    <h1 class="show-name">{{ $user->fullname }}</h1>
    <div class="rank-pill">{{ $user->role }}</div>

    <div class="info-list">
      <div class="info-group">
        <span class="i-label">Username</span>
        <span class="i-value">{{ $user->username }}</span>
      </div>
      <div class="info-group">
        <span class="i-label">Email</span>
        <span class="i-value">{{ $user->email }}</span>
      </div>
      <div class="info-group">
        <span class="i-label">Branch</span>
        <span class="i-value">{{ $user->branch->name ?? 'Head Office' }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">
            {{ in_array($user->role, ['manager', 'sr']) ? 'Employee ID' : 'Customer ID' }}
        </span>

        <span class="i-value">
            @if(in_array($user->role, ['manager', 'sr']))
                {{ $user->employee_id ? 'BRE100' . $user->employee_id : '--' }}

            @elseif($user->role === 'customer')
                {{ $user->customer_id ? 'BRC200' . $user->customer_id : '--' }}
            @else
                N/A
            @endif
        </span>
    </div>

      <div class="info-group">
        <span class="i-label">Created at</span>
        <span class="i-value">{{ $user->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</span>
      </div>

    </div>
    <div class="statement">
      <p class="statement-text">
        "This pass certifies that <strong>{{ $user->name }}</strong> is a verified user of
        <strong>{{ config('app.name') }}</strong>.
        Dedicated to high-performance engineering and operational excellence,
        this user plays a vital role in our mission to lead the industry."
      </p>
    </div>
  </div>

  <div class="card-footer-actions">

    <a href="{{ route('admin.users.edit', $user) }}" class="icon-btn edit-icon">
      <i class="fa-solid fa-pen"></i>
    </a>

    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
      onsubmit="return confirm('Are you sure?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="icon-btn delete-icon" style="border: none;">
        <i class="fa-solid fa-trash"></i>
      </button>
    </form>
  </div>

</div>
<a href="{{ route('dashboard.admin') }}" class="back-btn">
  ← Back
</a>

@endsection