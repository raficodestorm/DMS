@extends(getLayout())

@section('content')
<style>
  .settings-header {
    margin-bottom: 2rem;
  }

  .settings-header h2 {
    font-family: 'Cinzel', serif;
    font-size: 24px;
    color: var(--text-main);
  }

  .settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }

  /* Professional Setting Card */
  .setting-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 18px;
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1.2rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none !important;
    position: relative;
    overflow: hidden;
  }

  .setting-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary);
    box-shadow: 0 10px 25px var(--glass);
  }

  .setting-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
  }

  .setting-card:hover .setting-icon {
    background: var(--primary);
    color: #ffffff;
  }

  .setting-content h5 {
    color: var(--text-main);
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .setting-content p {
    color: var(--text-muted);
    font-size: 13px;
    margin-bottom: 0;
    line-height: 1.4;
  }

  .arrow-link {
    position: absolute;
    right: 1.5rem;
    top: 1.5rem;
    color: var(--border-color);
    font-size: 12px;
    transition: all 0.3s ease;
  }

  .setting-card:hover .arrow-link {
    color: var(--primary);
    transform: translateX(5px);
  }

  @media (max-width: 576px) {
    .settings-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="container-fluid p-0">
  <div class="settings-header">
    <h2>Account Settings</h2>
    <p style="color: var(--text-muted); font-size: 14px;">Manage your profile, security, and application preferences.
    </p>
  </div>

  <div class="settings-grid">

    <a href="{{ route('profile.index') }}" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="setting-content">
        <h5>My Profile</h5>
        <p>View your public profile and account details.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="{{ route('profile.edit') }}" class="setting-card">
      <div class="setting-icon">
        <div class="position-relative">
          <i class="fas fa-user-edit"></i>
        </div>
      </div>
      <div class="setting-content">
        <h5>Edit Profile</h5>
        <p>Update your name, email, and profile picture.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="{{ route('password.verify.form') }}" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-shield-alt"></i>
      </div>
      <div class="setting-content">
        <h5>Security</h5>
        <p>Change your password and secure your account.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="#" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-bell"></i>
      </div>
      <div class="setting-content">
        <h5>Notifications</h5>
        <p>Manage how you receive alerts and updates.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="#" class="setting-card" onclick="alert('Theme toggle logic integrated!')">
      <div class="setting-icon">
        <i class="fas fa-palette"></i>
      </div>
      <div class="setting-content">
        <h5>Appearance</h5>
        <p>Switch between light and dark cinematic modes.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

    <a href="#" class="setting-card">
      <div class="setting-icon">
        <i class="fas fa-lock"></i>
      </div>
      <div class="setting-content">
        <h5>Privacy</h5>
        <p>Control who can see your activity and data.</p>
      </div>
      <i class="fas fa-chevron-right arrow-link"></i>
    </a>

  </div>
</div>
@endsection