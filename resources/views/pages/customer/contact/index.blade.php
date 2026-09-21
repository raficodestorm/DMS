@extends('layouts.customerlayout')

@section('content')
<style>
  .contact-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
  }

  .contact-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: var(--primary);
    margin: 0;
    font-family: 'Cinzel', serif;
  }

  .contact-header p {
    font-size: 13px;
    color: var(--text-muted);
    margin: 4px 0 0;
    font-family: 'Inter', sans-serif;
  }

  .contact-cards-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 20px;
  }

  @media (min-width: 768px) {
    .contact-cards-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  .contact-card {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
  }

  .contact-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.09);
  }

  .contact-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
  }

  .card-sr::before {
    background: linear-gradient(90deg, #3131ff, #4facfe);
  }

  .card-manager::before {
    background: linear-gradient(90deg, #0ba360, #3cba92);
  }

  .card-role-badge {
    align-self: flex-start;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 5px 12px;
    border-radius: 50px;
    margin-bottom: 18px;
  }

  .badge-sr {
    background: rgba(49, 49, 255, 0.1);
    color: #3131ff;
  }

  .badge-manager {
    background: rgba(11, 163, 96, 0.1);
    color: #0ba360;
  }

  .contact-profile-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
  }

  .contact-avatar-wrap {
    width: 68px;
    height: 68px;
    min-width: 68px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--border-color);
    background: var(--background);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .card-sr .contact-avatar-wrap {
    border-color: rgba(49, 49, 255, 0.4);
  }

  .card-manager .contact-avatar-wrap {
    border-color: rgba(11, 163, 96, 0.4);
  }

  .contact-avatar-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .contact-name {
    font-size: 17px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 4px;
    font-family: 'Inter', sans-serif;
  }

  .contact-designation {
    font-size: 12.5px;
    color: var(--text-muted);
    margin: 0;
  }

  .contact-info-list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .contact-info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: var(--background);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    font-size: 13.5px;
    color: var(--text-main);
    font-family: 'Inter', sans-serif;
    transition: background 0.2s ease;
  }

  .contact-info-item:hover {
    background: var(--primary-soft);
  }

  .contact-info-item i {
    font-size: 15px;
    width: 20px;
    text-align: center;
    color: var(--primary);
  }

  .contact-info-item .info-label {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 2px;
  }

  .contact-info-item .info-val {
    font-weight: 600;
    color: var(--text-main);
    word-break: break-word;
    text-decoration: none;
  }

  .contact-info-item .info-val:hover {
    color: var(--primary);
  }

  .contact-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
  }

  .btn-contact-action {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }

  .btn-call {
    background: #0ba360;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(11, 163, 96, 0.25);
  }

  .btn-call:hover {
    background: #098850;
    color: #ffffff;
    transform: translateY(-2px);
  }

  .btn-email {
    background: var(--primary);
    color: #ffffff;
    box-shadow: 0 4px 12px var(--primary-soft);
  }

  .btn-email:hover {
    background: #2525e6;
    color: #ffffff;
    transform: translateY(-2px);
  }

  .empty-contact-state {
    text-align: center;
    padding: 30px 10px;
    color: var(--text-muted);
  }

  .empty-contact-state i {
    font-size: 36px;
    margin-bottom: 10px;
    opacity: 0.4;
  }
</style>

<div class="manage-card">
  <div class="contact-header animate__animated animate__fadeIn">
    <div>
      <h2><i class="fas fa-address-book me-2"></i> Contact & Support</h2>
      <p>Direct contacts for your assigned Sales Representative (SR) and Branch Manager</p>
    </div>
  </div>

  @include('components.alert')

  <div class="contact-cards-grid">

    {{-- Card 1: Assigned SR --}}
    <div class="contact-card card-sr animate__animated animate__fadeInUp" style="animation-delay: 0.05s;">
      <span class="card-role-badge badge-sr">
        <i class="fas fa-user-tie"></i> Sales Representative (SR)
      </span>

      @if($sr)
        @php
          $srName = $sr->fullname ?? ($sr->employee?->name ?? $sr->username);
          $srPhone = $sr->employee?->phone ?? ($sr->phone ?? null);
          $srEmail = $sr->email ?? ($sr->employee?->email ?? null);
          $srPhoto = $sr->profile_photo_path 
            ? (str_starts_with($sr->profile_photo_path, 'uploads/') ? asset($sr->profile_photo_path) : asset('uploads/' . $sr->profile_photo_path))
            : ($sr->employee?->photo 
                ? (str_starts_with($sr->employee->photo, 'uploads/') ? asset($sr->employee->photo) : asset('uploads/' . $sr->employee->photo))
                : 'https://ui-avatars.com/api/?name=' . urlencode($srName) . '&background=3131ff&color=fff');
        @endphp

        <div class="contact-profile-header">
          <div class="contact-avatar-wrap">
            <img src="{{ $srPhoto }}" alt="{{ $srName }}" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($srName) }}&background=3131ff&color=fff'">
          </div>
          <div>
            <h3 class="contact-name">{{ $srName }}</h3>
            <p class="contact-designation">{{ $sr->branch?->name ? $sr->branch->name . ' Branch' : 'Field Sales Representative' }}</p>
          </div>
        </div>

        <ul class="contact-info-list">
          <li class="contact-info-item">
            <i class="fas fa-phone-alt"></i>
            <div>
              <span class="info-label">Phone</span>
              @if($srPhone)
                <a href="tel:{{ $srPhone }}" class="info-val">{{ $srPhone }}</a>
              @else
                <span class="text-muted">Not available</span>
              @endif
            </div>
          </li>

          <li class="contact-info-item">
            <i class="fas fa-envelope"></i>
            <div>
              <span class="info-label">Email</span>
              @if($srEmail)
                <a href="mailto:{{ $srEmail }}" class="info-val">{{ $srEmail }}</a>
              @else
                <span class="text-muted">Not available</span>
              @endif
            </div>
          </li>
        </ul>

        <div class="contact-actions">
          @if($srPhone)
            <a href="tel:{{ $srPhone }}" class="btn-contact-action btn-call">
              <i class="fas fa-phone"></i> Call SR
            </a>
          @endif
          @if($srEmail)
            <a href="mailto:{{ $srEmail }}" class="btn-contact-action btn-email">
              <i class="fas fa-envelope"></i> Email
            </a>
          @endif
        </div>

      @else
        <div class="empty-contact-state">
          <i class="fas fa-user-slash"></i>
          <h5 class="fw-bold text-dark mb-1">No SR Assigned</h5>
          <p class="small m-0">You currently do not have an assigned Sales Representative.</p>
        </div>
      @endif
    </div>

    {{-- Card 2: Branch Manager --}}
    <div class="contact-card card-manager animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
      <span class="card-role-badge badge-manager">
        <i class="fas fa-user-shield"></i> Branch Manager
      </span>

      @if($manager)
        @php
          $mgrName = $manager->fullname ?? ($manager->employee?->name ?? $manager->username);
          $mgrPhone = $manager->employee?->phone ?? ($manager->phone ?? null);
          $mgrEmail = $manager->email ?? ($manager->employee?->email ?? null);
          $mgrPhoto = $manager->profile_photo_path 
            ? (str_starts_with($manager->profile_photo_path, 'uploads/') ? asset($manager->profile_photo_path) : asset('uploads/' . $manager->profile_photo_path))
            : ($manager->employee?->photo 
                ? (str_starts_with($manager->employee->photo, 'uploads/') ? asset($manager->employee->photo) : asset('uploads/' . $manager->employee->photo))
                : 'https://ui-avatars.com/api/?name=' . urlencode($mgrName) . '&background=0ba360&color=fff');
        @endphp

        <div class="contact-profile-header">
          <div class="contact-avatar-wrap">
            <img src="{{ $mgrPhoto }}" alt="{{ $mgrName }}" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($mgrName) }}&background=0ba360&color=fff'">
          </div>
          <div>
            <h3 class="contact-name">{{ $mgrName }}</h3>
            <p class="contact-designation">{{ $manager->branch?->name ? $manager->branch->name . ' Branch Manager' : 'Branch Manager' }}</p>
          </div>
        </div>

        <ul class="contact-info-list">
          <li class="contact-info-item">
            <i class="fas fa-phone-alt"></i>
            <div>
              <span class="info-label">Phone</span>
              @if($mgrPhone)
                <a href="tel:{{ $mgrPhone }}" class="info-val">{{ $mgrPhone }}</a>
              @else
                <span class="text-muted">Not available</span>
              @endif
            </div>
          </li>

          <li class="contact-info-item">
            <i class="fas fa-envelope"></i>
            <div>
              <span class="info-label">Email</span>
              @if($mgrEmail)
                <a href="mailto:{{ $mgrEmail }}" class="info-val">{{ $mgrEmail }}</a>
              @else
                <span class="text-muted">Not available</span>
              @endif
            </div>
          </li>
        </ul>

        <div class="contact-actions">
          @if($mgrPhone)
            <a href="tel:{{ $mgrPhone }}" class="btn-contact-action btn-call">
              <i class="fas fa-phone"></i> Call Manager
            </a>
          @endif
          @if($mgrEmail)
            <a href="mailto:{{ $mgrEmail }}" class="btn-contact-action btn-email">
              <i class="fas fa-envelope"></i> Email
            </a>
          @endif
        </div>

      @else
        <div class="empty-contact-state">
          <i class="fas fa-building-circle-xmark"></i>
          <h5 class="fw-bold text-dark mb-1">No Manager Found</h5>
          <p class="small m-0">No Branch Manager is currently assigned to your branch.</p>
        </div>
      @endif
    </div>

  </div>
</div>
@endsection
