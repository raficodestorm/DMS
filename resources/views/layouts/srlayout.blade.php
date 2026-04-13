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
        <div class="nav-link has-dropdown {{ isActive(['sr.users.*','sr.index.customers']) }}">
          <i class="fas fa-users"></i>
          <span>User Account</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen(['sr.users.*','sr.index.customers']) }}">
          <li>
            <a href="{{ route('sr.users.create') }}" class="sub-link {{ isActive('sr.users.create') }}">
              <i class="fas fa-user-plus me-1"></i> Add Account
            </a>
          </li>

          <li>
            <a href="{{ route('sr.index.customers') }}" class="sub-link {{ isActive('sr.index.customers') }}">
              <i class="fas fa-user-gear me-1"></i> Manage Accounts
            </a>
          </li>
        </ul>
      </li>

      <!-- Customers -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('customers.*') }}">
          <i class="fas fa-user-tie"></i>
          <span>Customer</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('customers.*') }}">
          <li>
            <a href="{{ route('customers.create') }}" class="sub-link {{ isActive('sr.customers.create') }}">
              <i class="fas fa-user-plus me-1"></i> Add Customer
            </a>
          </li>

          <li>
            <a href="{{ route('customers.index') }}" class="sub-link {{ isActive('sr.customers.index') }}">
              <i class="fas fa-users-cog me-1"></i> Manage Customers
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
  <script>
    const body = document.getElementById('body');
const toggleBtn = document.getElementById('toggleBtn');
const overlay = document.getElementById('overlay');
const profileBtn = document.getElementById('profileBtn');
const profileDropdown = document.getElementById('profileDropdown');

toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();

    if (window.innerWidth > 991)
        body.classList.toggle('sidebar-hidden');
    else
        body.classList.toggle('sidebar-open');
});

overlay.addEventListener('click', () => body.classList.remove('sidebar-open'));

profileBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    profileDropdown.classList.toggle('active');
});

window.addEventListener('click', () => profileDropdown.classList.remove('active'));

const navLinks = document.querySelectorAll('.nav-link');
const subLinks = document.querySelectorAll('.sub-link');

navLinks.forEach(link => {
    link.addEventListener('click', function () {

        navLinks.forEach(item => item.classList.remove('active-nav'));
        subLinks.forEach(item => item.classList.remove('active-sub'));

        this.classList.add('active-nav');

        if (this.classList.contains('has-dropdown'))
            this.parentElement.classList.toggle('open');
    });
});

subLinks.forEach(link => {
    link.addEventListener('click', function () {

        subLinks.forEach(item => item.classList.remove('active-sub'));

        this.classList.add('active-sub');

        this.closest('.nav-item')
            .querySelector('.nav-link')
            .classList.add('active-nav');

    });
});
function toggleTheme() {
  const html = document.documentElement;
  const theme = html.getAttribute("data-theme");

  if (theme === "dark") {
    html.removeAttribute("data-theme");
  } else {
    html.setAttribute("data-theme", "dark");
  }
}
  </script>
  @stack('scripts')
</body>

</html>