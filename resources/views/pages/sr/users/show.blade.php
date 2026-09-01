@extends('layouts.srlayout')

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
        <span class="i-label">Customer ID</span>
        <span class="i-value">{{ $user->customer_id ? 'BRC200' . $user->customer_id : '--' }}</span>
      </div>
      <div class="info-group">
        <span class="i-label">Email</span>
        <span class="i-value">{{ $user->email }}</span>
      </div>
      <div class="info-group">
        <span class="i-label">Zone</span>
        <span class="i-value">{{ $user->branch->name ?? '_' }}</span>
      </div>

      <div class="info-group">
        <span class="i-label">Joined</span>
        <span class="i-value">{{ $user->created_at->timezone(auth()->user()->timezone)->format('d M Y, h:i A') }}</span>
      </div>

    </div>
    <div class="statement">
      <p class="statement-text">
        "This pass certifies that <strong>{{ $user->name }}</strong> is a verified customer of
        <strong>{{ config('app.name') }}</strong>.
      </p>
    </div>
  </div>

  <div class="card-footer-actions">
    <a href="{{ route('sr.index.customers') }}" class="back-btn">
      ← Back
    </a>
  </div>

</div>

@endsection