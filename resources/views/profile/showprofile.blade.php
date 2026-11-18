@extends(getLayout())

@section('content')
<style>
  :root {
    --pf-radius: 16px;
  }

  .pf-container {
    max-width: 1040px;
    margin: 24px auto 50px;
    padding: 0 16px;
  }

  /* 2-Column Responsive Layout */
  .pf-layout {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 24px;
    align-items: start;
  }

  /* Left Side: Identity Card */
  .pf-card-user {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: var(--pf-radius);
    padding: 32px 24px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    position: sticky;
    top: 20px;
  }

  .pf-avatar-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 18px;
  }

  .pf-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    background: var(--background);
    border: 3px solid var(--primary-light, #4466ff);
    padding: 3px;
  }

  .pf-status-dot {
    position: absolute;
    bottom: 6px;
    right: 6px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 3px solid var(--section-bg);
  }

  .pf-status-dot.active {
    background: #16a34a;
  }

  .pf-status-dot.inactive {
    background: #dc2626;
  }

  .pf-user-name {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-main);
    margin: 0 0 4px;
    line-height: 1.25;
  }

  .pf-user-handle {
    font-size: 13px;
    color: var(--text-muted);
    margin: 0 0 14px;
    font-weight: 500;
  }

  .pf-role-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--primary);
    background: var(--primary-soft);
    padding: 5px 14px;
    border-radius: 50px;
  }

  .pf-divider {
    height: 1px;
    background: var(--border-color);
    margin: 22px 0;
  }

  .pf-mini-meta {
    display: flex;
    justify-content: space-around;
    text-align: center;
    margin-bottom: 22px;
  }

  .pf-mini-meta small {
    display: block;
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
  }

  .pf-mini-meta span {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-main);
  }

  .pf-btn-edit {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px;
    background: var(--primary);
    color: #ffffff !important;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px rgba(2, 2, 226, 0.2);
  }

  .pf-btn-edit:hover {
    background: var(--primary-light);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(2, 2, 226, 0.3);
  }

  /* Right Side: Details Cards */
  .pf-details-panel {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .pf-card-section {
    background: var(--section-bg);
    border: 1px solid var(--border-color);
    border-radius: var(--pf-radius);
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  }

  .pf-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
  }

  .pf-section-head h4 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .pf-section-head h4 i {
    color: var(--primary);
    font-size: 16px;
  }

  .pf-table-list {
    display: flex;
    flex-direction: column;
  }

  .pf-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
  }

  .pf-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .pf-row:first-child {
    padding-top: 0;
  }

  .pf-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .pf-label i {
    font-size: 14px;
    color: var(--primary);
    width: 18px;
    text-align: center;
  }

  .pf-value {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-main);
    text-align: right;
  }

  /* Responsive Breakpoints */
  @media (max-width: 860px) {
    .pf-layout {
      grid-template-columns: 1fr;
    }
    .pf-card-user {
      position: static;
    }
  }

  @media (max-width: 540px) {
    .pf-container {
      margin-top: 14px;
    }
    .pf-row {
      flex-direction: column;
      align-items: flex-start;
      gap: 4px;
      padding: 10px 0;
    }
    .pf-value {
      text-align: left;
    }
  }
</style>

<div class="pf-container">
  <div class="pf-layout">

    {{-- LEFT: Identity Profile Card --}}
    <div class="pf-card-user">
      <div class="pf-avatar-wrapper">
        <img
          src="{{ auth()->user()->profile_photo_path ? (str_starts_with(auth()->user()->profile_photo_path, 'uploads/') ? asset(auth()->user()->profile_photo_path) : asset('uploads/' . auth()->user()->profile_photo_path)) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->fullname).'&background=0202e2&color=fff' }}"
          alt="Avatar" class="pf-avatar">
        <span class="pf-status-dot {{ Auth::user()->status === 'active' ? 'active' : 'inactive' }}"
              title="{{ Auth::user()->status === 'active' ? 'Account Active' : 'Account Inactive' }}"></span>
      </div>

      <h3 class="pf-user-name">{{ Auth::user()->fullname }}</h3>
      <p class="pf-user-handle">&#64;{{ Auth::user()->username }}</p>

      <div>
        <span class="pf-role-chip">
          <i class="fa-solid fa-shield-halved"></i> {{ Auth::user()->role ?? 'User' }}
        </span>
      </div>

      <div class="pf-divider"></div>

      <div class="pf-mini-meta">
        <div>
          <small>Status</small>
          <span>
            @if(Auth::user()->status === 'active')
              <span class="text-success fw-bold">Active</span>
            @else
              <span class="text-danger fw-bold">Inactive</span>
            @endif
          </span>
        </div>
        <div>
          <small>UID</small>
          <span>#{{ Auth::user()->id }}</span>
        </div>
      </div>

      <a href="{{ route('profile.edit') }}" class="pf-btn-edit">
        <i class="fa-solid fa-pen-to-square"></i> Edit Profile
      </a>
    </div>

    {{-- RIGHT: Detailed Panels --}}
    <div class="pf-details-panel">

      {{-- Account Credentials & System Info --}}
      <div class="pf-card-section">
        <div class="pf-section-head">
          <h4><i class="fa-regular fa-user"></i> General Information</h4>
          <span class="badge bg-light text-muted border" style="font-size: 11px;">System Verified</span>
        </div>

        <div class="pf-table-list">
          <div class="pf-row">
            <span class="pf-label"><i class="fa-regular fa-id-badge"></i> Full Name</span>
            <span class="pf-value">{{ Auth::user()->fullname }}</span>
          </div>

          <div class="pf-row">
            <span class="pf-label"><i class="fa-solid fa-at"></i> Username</span>
            <span class="pf-value">{{ Auth::user()->username }}</span>
          </div>

          <div class="pf-row">
            <span class="pf-label"><i class="fa-regular fa-envelope"></i> Email Address</span>
            <span class="pf-value">{{ Auth::user()->email }}</span>
          </div>

          <div class="pf-row">
            <span class="pf-label"><i class="fa-solid fa-code-branch"></i> Assigned Branch</span>
            <span class="pf-value">{{ Auth::user()->branch->name ?? 'Head Office' }}</span>
          </div>

          <div class="pf-row">
            <span class="pf-label"><i class="fa-regular fa-calendar-days"></i> Member Since</span>
            <span class="pf-value">{{ Auth::user()->created_at->format('d M Y, h:i A') }}</span>
          </div>

          <div class="pf-row">
            <span class="pf-label"><i class="fa-regular fa-clock"></i> Last Updated</span>
            <span class="pf-value">{{ Auth::user()->updated_at->diffForHumans() }}</span>
          </div>
        </div>
      </div>

      {{-- Location / Customer Specific Info (If Customer) --}}
      @if(Auth::user()->isCustomer() && Auth::user()->customer)
      <div class="pf-card-section">
        <div class="pf-section-head">
          <h4><i class="fa-solid fa-location-dot"></i> Location & Customer Details</h4>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;">Customer Data</span>
        </div>

        <div class="pf-table-list">
          <div class="pf-row">
            <span class="pf-label"><i class="fa-solid fa-store"></i> Shop Name</span>
            <span class="pf-value">{{ Auth::user()->customer->shop_name ?? '—' }}</span>
          </div>

          <div class="pf-row">
            <span class="pf-label"><i class="fa-solid fa-globe"></i> Country</span>
            <span class="pf-value">{{ Auth::user()->customer->country ?: '—' }}</span>
          </div>

          <div class="pf-row">
            <span class="pf-label"><i class="fa-solid fa-city"></i> City</span>
            <span class="pf-value">{{ Auth::user()->customer->city ?: '—' }}</span>
          </div>

          <div class="pf-row">
            <span class="pf-label"><i class="fa-solid fa-map-pin"></i> Address</span>
            <span class="pf-value">{{ Auth::user()->customer->address ?: '—' }}</span>
          </div>
        </div>
      </div>
      @endif

    </div>

  </div>
</div>
@endsection
