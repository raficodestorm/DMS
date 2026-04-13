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
        <div class="nav-link has-dropdown {{ isActive(['admin.users.*','admin.index.*']) }}">
          <i class="fas fa-users"></i>
          <span>Users</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen(['admin.users.*','admin.index.*']) }}">

          <li>
            <a href="{{ route('admin.users.create') }}" class="sub-link {{ isActive('admin.users.create') }}">
              <i class="fas fa-user-plus me-1"></i> Add User
            </a>
          </li>

          <li>
            <a href="{{ route('admin.index.managers') }}" class="sub-link {{ isActive('admin.index.managers') }}">
              <i class="fas fa-user-gear me-1"></i> Manage Managers
            </a>
          </li>

          <li>
            <a href="{{ route('admin.index.srs') }}" class="sub-link {{ isActive('admin.index.srs') }}">
              <i class="fas fa-user-gear me-1"></i> Manage SRs
            </a>
          </li>

          <li>
            <a href="{{ route('admin.index.customers') }}" class="sub-link {{ isActive('admin.index.customers') }}">
              <i class="fas fa-user-gear me-1"></i> Manage Customers
            </a>
          </li>

        </ul>
      </li>

      <!-- Branches -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('admin.branches.*') }}">
          <i class="fas fa-code-branch"></i>
          <span>Branches</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.branches.*') }}">
          <li>
            <a href="{{ route('admin.branches.create') }}" class="sub-link {{ isActive('admin.branches.create') }}">
              <i class="fas fa-plus-circle me-1"></i> Add Branch
            </a>
          </li>

          <li>
            <a href="{{ route('admin.branches.index') }}" class="sub-link {{ isActive('admin.branches.index') }}">
              <i class="fas fa-list me-1"></i> Manage Branches
            </a>
          </li>
        </ul>
      </li>

      <!-- Employees -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('admin.employees.*') }}">
          <i class="fas fa-user-tie"></i>
          <span>Employees</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.employees.*') }}">
          <li>
            <a href="{{ route('admin.employees.create') }}" class="sub-link {{ isActive('admin.employees.create') }}">
              <i class="fas fa-user-plus me-1"></i> Add Employee
            </a>
          </li>

          <li>
            <a href="{{ route('admin.employees.index') }}" class="sub-link {{ isActive('admin.employees.index') }}">
              <i class="fas fa-users-cog me-1"></i> All Employees
            </a>
          </li>

        </ul>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.customers.index') }}" class="nav-link {{ isActive('admin.customer.index') }}">
          <i class="fas fa-users-cog"></i> Manage Customers
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.stock.in.requests.index') }}"
          class="nav-link {{ isActive('admin.stock.in.requests.index') }}">
          <i class="fas fa-users-cog"></i> Stock-in Request
        </a>
      </li>

      <!-- category -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('admin.categories.*') }}">
          <i class="fa-solid fa-folder-open"></i>
          <span>Category</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.categories.*') }}">
          <li>
            <a href="{{ route('admin.categories.create') }}" class="sub-link {{ isActive('admin.categories.create') }}">
              <i class="fa-solid fa-folder-plus me-1"></i> Add Category
            </a>
          </li>

          <li>
            <a href="{{ route('admin.categories.index') }}" class="sub-link {{ isActive('admin.categories.index') }}">
              <i class="fa-solid fa-folder-open me-1"></i> All Categories
            </a>
          </li>

        </ul>
      </li>


      <!-- Products -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('admin.suppliers.*') }}">
          <i class="fa-solid fa-box"></i>
          <span>Supplier</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.suppliers.*') }}">
          <li>
            <a href="{{ route('admin.suppliers.create') }}" class="sub-link {{ isActive('admin.suppliers.create') }}">
              <i class="fa-solid fa-plus me-1"></i> Add Supplier
            </a>
          </li>

          <li>
            <a href="{{ route('admin.suppliers.index') }}" class="sub-link {{ isActive('admin.suppliers.index') }}">
              <i class="fa-solid fa-boxes-stacked me-1"></i> All Suppliers
            </a>
          </li>

        </ul>
      </li>

      <!-- Products -->
      <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('admin.products.*') }}">
          <i class="fa-solid fa-box"></i>
          <span>Product</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.products.*') }}">
          <li>
            <a href="{{ route('admin.products.create') }}" class="sub-link {{ isActive('admin.products.create') }}">
              <i class="fa-solid fa-plus me-1"></i> Add Product
            </a>
          </li>

          <li>
            <a href="{{ route('admin.products.index') }}" class="sub-link {{ isActive('admin.products.index') }}">
              <i class="fa-solid fa-boxes-stacked me-1"></i> All Products
            </a>
          </li>

        </ul>
      </li>

    </ul>




    {{-- <div class="side-footer-float">
      <div class="settings-menu"><i class="fas fa-settings"></i>Settings</div>
      <div class="side-profile">
        <div class="img-container"><img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="profile"
            style="width:100%;height:100%;object-fit:cover;display:block;"></div>
        <span>{{ auth()->check() ? auth()->user()->fullname : 'Guest' }}</span>
      </div>
    </div> --}}
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

      <div class="notification-wrapper">

        <div class="notification-icon" onclick="toggleNotifDropdown()">
          <i class="fas fa-bell"></i>

          @if(auth()->user()->unreadNotifications->count() > 0)
          <span class="notif-count">
            {{ auth()->user()->unreadNotifications->count() }}
          </span>
          @endif
        </div>

        <div class="notification-dropdown" id="notifDropdown">

          <div class="notif-header">Notifications</div>

          @forelse(auth()->user()->unreadNotifications as $note)
          <a href="{{ $note->data['url'] }}" class="notif-item unread" onclick="markAsRead(event, '{{ $note->id }}')">

            <div class="notif-title">
              {{ $note->data['title'] }}
            </div>

            <div class="notif-msg">
              {{ $note->data['message'] }}
            </div>

            <div class="notif-time">
              {{ $note->created_at->diffForHumans() }}
            </div>
          </a>
          @empty
          <div class="no-notif">No new notifications</div>
          @endforelse

        </div>

      </div>

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


function toggleNotifDropdown() {
    let dropdown = document.getElementById('notifDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function markAsRead(event, id) {
    fetch('/notifications/read/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}
  </script>
  @stack('scripts')
</body>

</html>