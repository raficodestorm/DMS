<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>R.Electric</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <!-- Vite CSS + JS -->
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="stylesheet" href="{{ asset('css/color-root.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sidenavbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/adminstyle.css') }}">

</head>

<body id="body">

  <div class="sidebar-overlay" id="overlay"></div>

  <aside class="sidebar">
    <div class="sidebar-brand">R.Electric</div>

    <ul class="nav-menu">

      <!-- Dashboard -->
      <li class="nav-item">
        <a href="{{ route('dashboards') }}" class="nav-link {{ isActive('dashboards') }}">
          <i class="fas fa-gauge-high"></i> Dashboard
        </a>
      </li>

      <!-- Users -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive(['manager.users.*','manager.index.srs']) }}">
          <i class="fas fa-users"></i>
          <span>User Accounts</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen(['manager.users.*','manager.index.srs']) }}">
          <li>
            <a href="{{ route('manager.users.create') }}" class="sub-link {{ isActive('manager.users.create') }}">
              <i class="fas fa-user-plus me-1"></i> Add User
            </a>
          </li>

          <li>
            <a href="{{ route('manager.index.srs') }}" class="sub-link {{ isActive('manager.index.srs') }}">
              <i class="fas fa-user-gear me-1"></i> Manage SRs
            </a>
          </li>

          <li>
            <a href="{{ route('manager.index.customers') }}" class="sub-link {{ isActive('manager.index.customers') }}">
              <i class="fas fa-user-gear me-1"></i> Manage Customers
            </a>
          </li>

        </ul>
      </li>

      <!-- Employees -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('manager.employees.*') }}">
          <i class="fas fa-user-tie"></i>
          <span>Employees</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('manager.employees.*') }}">
          <li>
            <a href="{{ route('manager.employees.create') }}"
              class="sub-link {{ isActive('manager.employees.create') }}">
              <i class="fas fa-user-plus me-1"></i> Add Employee
            </a>
          </li>

          <li>
            <a href="{{ route('manager.employees.index') }}" class="sub-link {{ isActive('manager.employees.index') }}">
              <i class="fas fa-users-cog me-1"></i> Manage Employees
            </a>
          </li>
        </ul>
      </li>

      <!-- Stock -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('manager.stock.*') }}">
          <i class="fas fa-user-tie"></i>
          <span>Stock</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('manager.stock.*') }}">
          <li>
            <a href="{{ route('manager.stock.index') }}" class="sub-link {{ isActive('manager.stock.index') }}">
              <i class="fas fa-user-plus me-1"></i> My Stock
            </a>
          </li>

          <li>
            <a href="{{ route('manager.stock.in.create') }}" class="sub-link {{ isActive('manager.stock.in.create') }}">
              <i class="fas fa-user-plus me-1"></i> Stock-in
            </a>
          </li>

          <li>
            <a href="{{ route('manager.stock.in.requests.index') }}"
              class="sub-link {{ isActive('manager.stock.in.requests.index') }}">
              <i class="fas fa-user-plus me-1"></i> Stock-in Requests
            </a>
          </li>
        </ul>
      </li>

    </ul>
  </aside>


  <header class="custom-navbar">

    <div class="nav-left">

      <div class="menu-toggle" id="toggleBtn">
        <i style="color:var(--primary);" class="fa-solid fa-bars"></i>
      </div>

      <div class="search-box" style="position: relative;">
        <input type="text" id="mainSearch" placeholder="Search articles or categories..." autocomplete="off">
        <div id="search-results" class="search-suggestions-box"></div>
      </div>

    </div>


    <div class="nav-right">
      <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fa-solid fa-moon"></i>
      </button>

      <div class="profile-wrapper" id="profileBtn">

        <div class="profile-info" style="display:flex;align-items:center;cursor:pointer;gap:5px;">


          <div
            style="width:40px;height:40px;min-width:40px;overflow:hidden;border-radius:50%;border:2px solid var(--primary);background:#2A3038;display:flex;align-items:center;justify-content:center;">

            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="profile"
              style="width:100%;height:100%;object-fit:cover;display:block;">

          </div>

          <span class="profile-name" style="font-weight:600;color:var(--primary);white-space:nowrap;margin-left:2px;">

            {{ auth()->check() ? auth()->user()->username : 'Guest' }}

          </span>

          <i class="mdi mdi-menu-down" style="color:var(--primary);"></i>

        </div>


        <div class="dropdown-box" id="profileDropdown">
          <ul>
            <li><a class="dropdown-link" href="{{ route('dashboards') }}">Dashboard</a></li>
            <li><a class="dropdown-link" href="{{ route('profile.index') }}">Profile</a></li>
            <li><a class="dropdown-link" href="{{ route('settings') }}">settings</a></li>
            <li class="dropdown-link">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger">Logout</button>
              </form>
            </li>
          </ul>
        </div>

      </div>
    </div>

  </header>


  <main class="main-content">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @if(session('success'))
  <script>
    Swal.fire({
    html: `
        <div class="success-wrapper">
            <div class="success-circle">
                <div class="checkmark"></div>
            </div>
            <h2 class="success-title">Success</h2>
            <p class="success-text">{{ session('success') }}</p>
        </div>
    `,
    showConfirmButton: false,
    timer: 2200,
    background: 'transparent',
    backdrop: 'rgba(0,0,0,0.3)',
    customClass: {
        popup: 'success-popup'
    }
});
  </script>
  @endif

  @if(session('error'))
  <script>
    Swal.fire({
    html: `
        <div class="error-wrapper">
            <div class="error-circle">
                <div class="cross-mark">✕</div>
            </div>
            <h2 class="error-title">Error</h2>
            <p class="error-text">{{ session('error') }}</p>
        </div>
    `,
    showConfirmButton: false,
    timer: 2600,
    background: 'transparent',
    backdrop: 'rgba(0,0,0,0.35)',
    customClass: {
        popup: 'error-popup'
    }
});
  </script>
  @endif

  <script>
    (function(window, document) {
    "use strict";

    // ১. DOM Elements ক্যাশ করা
    const body = document.getElementById('body');
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('overlay');
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    // ২. সাইডবার টগল
    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (window.innerWidth > 991) body.classList.toggle('sidebar-hidden');
            else body.classList.toggle('sidebar-open');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => body.classList.remove('sidebar-open'));
    }

    // ৩. প্রোফাইল ড্রপডাউন
    if (profileBtn) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });
    }

    window.addEventListener('click', () => {
        if (profileDropdown) profileDropdown.classList.remove('active');
    });

    // ৪. নেভিগেশন লিংক লজিক
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.nav-link').forEach(item => item.classList.remove('active-nav'));
            this.classList.add('active-nav');
            if (this.classList.contains('has-dropdown')) {
                this.parentElement.classList.toggle('open');
            }
        });
    });

    
    window.toggleTheme = function() {
        const html = document.documentElement;
        const isDark = html.getAttribute("data-theme") === "dark";
        
        if (isDark) {
            html.removeAttribute("data-theme");
            localStorage.setItem('theme', 'light');
        } else {
            html.setAttribute("data-theme", "dark");
            localStorage.setItem('theme', 'dark');
        }
        console.log("Theme switched!");
    };

    // ৬. পেজ লোড হওয়ার সময় থিম চেক করা
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute("data-theme", "dark");
    }

})(window, document);
  </script>
  @stack('scripts')
</body>

</html>