@extends('layouts.adminlayout')

@section('content')
<style>
  .profile-container {
    max-width: 900px;
    margin: 0 auto;
  }

  .profile-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  }

  .profile-header {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    height: 120px;
    position: relative;
  }

  .profile-avatar-wrapper {
    position: absolute;
    bottom: -50px;
    left: 40px;
  }

  .profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 5px solid var(--section-bg);
    background: var(--background);
    object-fit: cover;
  }

  .profile-body {
    padding: 70px 40px 40px;
  }

  .info-label {
    color: var(--text-muted);
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
  }

  .info-value {
    color: var(--text-main);
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 20px;
  }

  .edit-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--primary-soft);
    color: var(--primary);
    padding: 10px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.3s ease;
    border: 1px solid transparent;
  }

  .edit-link:hover {
    background: var(--primary);
    color: #ffffff;
    transform: translateY(-2px);
  }

  @media (max-width: 768px) {
    .profile-avatar-wrapper {
      left: 50%;
      transform: translateX(-50%);
    }

    .profile-body {
      text-align: center;
      padding: 70px 20px 30px;
    }
  }
</style>

<div class="profile-container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="font-size: 22px; font-family: 'Cinzel', serif; color: var(--text-main);">My Profile</h2>
    <span class="badge p-2 text-uppercase" style="color: var(--primary); background: var(--primary-soft);">
      {{ Auth::user()->role ?? 'Administrator' }}
    </span>
  </div>

  <div class="profile-card">
    <div class="profile-header">
      <div class="profile-avatar-wrapper">
        <img
          src="{{ asset('storage/' . auth()->user()->profile_photo_path) ?? 'https://ui-avatars.com/api/?name='.Auth::user()->username.'&background=3131ff&color=fff' }}"
          alt="Profile" class="profile-avatar shadow">
      </div>
    </div>

    <div class="profile-body">
      <div class="row">
        <div class="col-md-6">
          <div class="info-label">Full Name</div>
          <div class="info-value">{{ Auth::user()->fullname }}</div>

          <div class="info-label">Email Address</div>
          <div class="info-value">{{ Auth::user()->email }}</div>
          <div class="info-label">Username</div>
          <div class="info-value">{{ Auth::user()->username }}</div>
        </div>

        <div class="col-md-6">
          <div class="info-label">Joined Date</div>
          <div class="info-value">{{ Auth::user()->created_at->format('d M, Y') }}</div>

          <div class="info-label">Account Status</div>
          <div class="info-value">
            <span style="color: var(--success);"><i class="mdi mdi-check-decagram"></i> Verified</span>
          </div>
        </div>
      </div>

      <hr style="border-top: 1px solid var(--border-color); margin: 30px 0;">

      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <p class="mb-0 text-muted-custom small" style="color: var(--text-muted);">
          Last profile update: {{ Auth::user()->updated_at->diffForHumans() }}
        </p>
        <a href="{{ route('profile.edit') }}" class="edit-link">
          <i class="mdi mdi-account-edit-outline"></i> Edit Profile Details
        </a>
      </div>
    </div>
  </div>
</div>
@endsection