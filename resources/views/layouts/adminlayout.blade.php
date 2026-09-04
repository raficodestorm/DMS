<!DOCTYPE html>
<html lang="en">

<head>
  <script>
    if (localStorage.getItem('theme') === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name') }} | Admin panel</title>
  <link rel="icon" type="image/png" sizes="35x35" href="{{ asset('image/relectric-r-logo.webp') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

  <!-- Vite CSS + JS -->
  @vite(['resources/css/app.css','resources/js/app.js'])
  <link rel="stylesheet" href="{{ asset('css/color-root.css') }}">
  <link rel="stylesheet" href="{{ asset('css/buttons.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sidenavbar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/adminstyle.css') }}">

</head>

<body id="body">

  <div class="sidebar-overlay" id="overlay"></div>

  <aside class="sidebar">
    <div class="sidebar-brand">
      <img src="{{ asset('image/relectric-logo.png') }}" alt="Logo" class="sidebar-logo img-fluid"
        style="width: 170px; height: 60x;">
    </div>

    <ul class="nav-menu">

      <!-- Dashboard -->
      <li class="nav-item">
        <a href="{{ route('dashboards') }}" class="nav-link {{ isActive('dashboards') }}">
          <i class="fas fa-gauge-high"></i> Dashboard
        </a>
      </li>

      <!-- Reports -->
      <li class="nav-item">
        <a href="{{ route('admin.report.index') }}" class="nav-link {{ isActive('admin.report.*') }}">
          <i class="fas fa-chart-pie"></i> Reports & Analytics
        </a>
      </li>

      <!-- Users -->
      <li class="nav-item {{ request()->routeIs('admin.users.*', 'admin.index.*') ? 'open' : '' }}">
        <div class="nav-link has-dropdown {{ isActive(['admin.users.*','admin.index.*']) }}">
          <i class="fas fa-users"></i>
          <span>Users</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen(['admin.users.*','admin.index.*']) }}">

          <li>
            <a href="{{ route('admin.users.create') }}" class="sub-link {{ isActive('admin.users.create') }}">
              <i class="fas fa-user-plus me-1"></i> Add User Account
            </a>
          </li>

          <li>
            <a href="{{ route('admin.index.users') }}" class="sub-link {{ isActive('admin.index.users') }}">
              <i class="fas fa-user-gear me-1"></i> All User Accounts
            </a>
          </li>

      

        </ul>
      </li>

      <!-- Branches -->
      <li class="nav-item {{ request()->routeIs('admin.branches.*') ? 'open' : '' }}">
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
      <li class="nav-item {{ request()->routeIs('admin.employees.*') ? 'open' : '' }}">
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
        <a href="{{ route('admin.customers.index') }}" class="nav-link {{ isActive('admin.customers.*') }}">
          <i class="fas fa-handshake"></i> Manage Customers
        </a>
      </li>


      <!-- Stock -->
      <li
        class="nav-item {{ request()->routeIs('admin.stock.*', 'admin.stocks.*', 'admin.stock-transfer.*') ? 'open' : '' }}">
        <div
          class="nav-link has-dropdown {{ isActive(['admin.stock.*', 'admin.stocks.*', 'admin.stock-transfer.*']) }}">
          <i class="fas fa-boxes-stacked"></i>
          <span>Stock</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen(['admin.stock.*', 'admin.stocks.*', 'admin.stock-transfer.*']) }}">
          <li>
            <a href="{{ route('admin.stocks.all') }}" class="sub-link {{ isActive('admin.stocks.all') }}">
              <i class="fas fa-layer-group me-1"></i>Stock Summary
            </a>
          </li>

          <li>
            <a href="{{ route('admin.stock.in.requests.index') }}"
              class="sub-link {{ isActive('admin.stock.in.requests.index') }}">
              <i class="fas fa-clipboard-list me-1"></i> Stock-in Request
            </a>
          </li>

          <li>
            <a href="{{ route('admin.stock-transfer.index') }}"
              class="sub-link {{ isActive('admin.stock-transfer.*') }}">
              <i class="fas fa-truck-moving me-1"></i> Stock Transfer
            </a>
          </li>

        </ul>
      </li>


      <!-- orders -->
      <li class="nav-item {{ request()->routeIs('admin.order.*', 'admin.orders.*') ? 'open' : '' }}">
        <div class="nav-link has-dropdown {{ isActive(['admin.order.*', 'admin.orders.*']) }}">
          <i class="fa-solid fa-cart-shopping"></i>
          <span>Order</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen(['admin.order.*', 'admin.orders.*']) }}">

          <li>
            <a href="{{ route('admin.order.index') }}" class="sub-link {{ isActive('admin.order.index') }}">
              <i class="fa-solid fa-boxes-stacked me-1"></i> All orders
            </a>
          </li>

          <li>
            <a href="{{ route('admin.order.all.customers') }}"
              class="sub-link {{ isActive('admin.order.all.customers') }}">
              <i class="fas fa-user-plus me-1"></i> Cust based orders
            </a>
          </li>

          <li>
            <a href="{{ route('admin.order.all.srs') }}" class="sub-link {{ isActive('admin.order.all.srs') }}">
              <i class="fas fa-user-plus me-1"></i> Sr based orders
            </a>
          </li>

          <li>
            <a href="{{ route('admin.order.all.branches') }}"
              class="sub-link {{ isActive('admin.order.all.branches') }}">
              <i class="fas fa-user-plus me-1"></i> Branch based orders
            </a>
          </li>

        </ul>
      </li>

      <li class="nav-item">
        <a href="{{ route('admin.payments.index') }}" class="nav-link {{ isActive('admin.payments.index') }}">
          <i class="fas fa-wallet"></i> Transactions
        </a>
      </li>

      <!-- Product Return -->
      <li class="nav-item {{ request()->routeIs('admin.return.*') ? 'open' : '' }}">
        <div class="nav-link has-dropdown {{ isActive('admin.return.*') }}">
          <i class="fas fa-undo"></i>
          <span>Product Return</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.return.*') }}">
          <li>
            <a href="{{ route('admin.return.index') }}" class="sub-link {{ isActive('admin.return.index') }}">
              <i class="fas fa-list me-1"></i> Return Dashboard
            </a>
          </li>
        </ul>
      </li>

      <!-- Bonus Management -->
      <li class="nav-item {{ request()->routeIs('admin.bonuses.*') ? 'open' : '' }}">
        <div class="nav-link has-dropdown {{ isActive('admin.bonuses.*') }}">
          <i class="fas fa-gift"></i>
          <span>Bonus</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.bonuses.*') }}">
          <li>
            <a href="{{ route('admin.bonuses.create') }}" class="sub-link {{ isActive('admin.bonuses.create') }}">
              <i class="fas fa-plus-circle me-1"></i> Add Bonus
            </a>
          </li>
          <li>
            <a href="{{ route('admin.bonuses.index') }}" class="sub-link {{ isActive('admin.bonuses.index') }}">
              <i class="fas fa-list me-1"></i> Bonus History
            </a>
          </li>
        </ul>
      </li>

      <!-- Company Global Costs -->
      <li class="nav-item {{ request()->routeIs('admin.company_costs.*', 'admin.costs.*') ? 'open' : '' }}">
        <div class="nav-link has-dropdown {{ isActive(['admin.company_costs.*', 'admin.costs.*']) }}">
          <i class="fas fa-money-bill-wave"></i>
          <span>Global Expenses</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen(['admin.company_costs.*', 'admin.costs.*']) }}">
          <li>
            <a href="{{ route('admin.costs.dashboard') }}" class="sub-link {{ isActive('admin.costs.dashboard') }}">
              <i class="fas fa-chart-line me-1"></i> Cost Dashboard
            </a>
          </li>
          <li>
            <a href="{{ route('admin.company_costs.create') }}"
              class="sub-link {{ isActive('admin.company_costs.create') }}">
              <i class="fas fa-plus-circle me-1"></i> Record Global Cost
            </a>
          </li>
          <li>
            <a href="{{ route('admin.company_costs.index') }}"
              class="sub-link {{ isActive('admin.company_costs.index') }}">
              <i class="fas fa-list me-1"></i> Expense History
            </a>
          </li>
        </ul>
      </li>

      <!-- category -->
      <li class="nav-item {{ request()->routeIs('admin.categories.*') ? 'open' : '' }}">
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
      <li class="nav-item {{ request()->routeIs('admin.suppliers.*') ? 'open' : '' }}">
        <div class="nav-link has-dropdown {{ isActive('admin.suppliers.*') }}">
          <i class="fa-solid fa-truck-field"></i>
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
      <li class="nav-item {{ request()->routeIs('admin.products.*') ? 'open' : '' }}">
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



      <!-- offer -->
      <li class="nav-item {{ request()->routeIs('admin.offers.*') ? 'open' : '' }}">
        <div class="nav-link has-dropdown {{ isActive('admin.offers.*') }}">
          <i class="fa-solid fa-tags"></i>
          <span>Offer</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.offers.*') }}">
          <li>
            <a href="{{ route('admin.offers.create') }}" class="sub-link {{ isActive('admin.offers.create') }}">
              <i class="fa-solid fa-plus me-1"></i> Create Offer
            </a>
          </li>

          <li>
            <a href="{{ route('admin.offers.index') }}" class="sub-link {{ isActive('admin.offers.index') }}">
              <i class="fa-solid fa-boxes-stacked me-1"></i> All Offers
            </a>
          </li>

        </ul>
      </li>


      <li class="nav-item">
        <a href="{{ route('admin.deductions.index') }}" class="nav-link {{ isActive('admin.deductions.index') }}">
          <i class="fa-solid fa-scissors"></i> Deduction
        </a>
      </li>
      <!-- deductin -->
      {{-- <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive('admin.offers.*') }}">
          <i class="fa-solid fa-scissors"></i>
          <span>Deduction</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen('admin.deductions.*') }}">
          <li>
            <a href="{{ route('admin.deductions.create') }}" class="sub-link {{ isActive('admin.deductions.create') }}">
              <i class="fa-solid fa-plus me-1"></i> Create Deduction
            </a>
          </li>

          <li>
            <a href="{{ route('admin.deductions.index') }}" class="sub-link {{ isActive('admin.deductions.index') }}">
              <i class="fa-solid fa-boxes-stacked me-1"></i> All Deductions
            </a>
          </li>

        </ul>
      </li> --}}


      <!-- Stock cut-->
      <li class="nav-item">
        <a href="{{ route('admin.stock.cut.cuts.index') }}" class="nav-link {{ isActive('admin.stock.cut.index') }}">
          <i class="fas fa-handshake"></i> Stock Cut
        </a>
      </li>



    </ul>




    {{-- <div class="side-footer-float">
      <div class="settings-menu"><i class="fas fa-settings"></i>Settings</div>
      <div class="side-profile">
        <div class="img-container"><img
            src="{{ auth()->user()->profile_photo_path ? (str_starts_with(auth()->user()->profile_photo_path, 'uploads/') ? asset(auth()->user()->profile_photo_path) : asset('uploads/' . auth()->user()->profile_photo_path)) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->username).'&background=3131ff&color=fff' }}"
            alt="profile" style="width:100%;height:100%;object-fit:cover;display:block;"></div>
        <span>{{ auth()->check() ? auth()->user()->fullname : 'Guest' }}</span>
      </div>
    </div> --}}
  </aside>


  <header class="custom-navbar">

    <div class="nav-left">

      <div class="menu-toggle" id="toggleBtn">
        <i style="color:var(--primary);" class="fa-solid fa-bars"></i>
      </div>

      <!-- <div class="search-box" style="position: relative;">
        <input type="text" id="mainSearch" placeholder="Search articles or categories..." autocomplete="off">
        <div id="search-results" class="search-suggestions-box"></div>
      </div> -->

    </div>


    <div class="nav-right">


    {{-- Calculator Toggle --}}
      <button class="theme-toggle" onclick="toggleCalculator(event)" title="Calculator">
        <i class="fa-solid fa-calculator"></i>
      </button>

      {{-- Notifications --}}
      <div class="notification-wrapper">
        <div class="notification-icon" id="notifBtn" onclick="toggleNotifDropdown(event)">
          <i class="fas fa-bell"></i>

          @if(auth()->user()->unreadNotifications->count() > 0)
          <span class="notif-count">
            {{ auth()->user()->unreadNotifications->count() }}
          </span>
          @endif
        </div>

        <div class="notification-dropdown" id="notifDropdown">
          <div class="notif-header">Notifications</div>
          <div class="notif-body">
            @forelse(auth()->user()->unreadNotifications as $note)
            <a href="{{ route('notifications.markAndRedirect', $note->id) }}" class="notif-item unread" data-id="{{ $note->id }}">
              <div class="notif-title">
                {{ $note->data['title'] }}
              </div>
              <div class="notif-msg">
                @if(is_array($note->data['message']))
                {{ $note->data['message']['text'] }}
                <span class="text-primary fw-bold">
                  {{ $note->data['message']['from'] }} branch
                </span>
                @else
                {{ $note->data['message'] }}
                @endif
              </div>
              <div class="notif-time" data-timestamp="{{ $note->created_at->toIso8601String() }}">
                <i class="fa-regular fa-clock"></i> {{ $note->created_at->diffForHumans() }}
              </div>
            </a>
            @empty
            <div class="no-notif">
              <i class="fa-solid fa-bell-slash"></i>
              <p>No new notifications</p>
            </div>
            @endforelse
          </div>
          <div class="notif-footer" style="padding: 8px 12px; text-align: center; border-top: 1px solid var(--border-color, #e2e8f0); background: var(--section-bg, #fff);">
            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="deleteAllNotifications(event)" style="font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; gap: 6px; border-radius: 6px; padding: 6px 12px;">
              <i class="fa-solid fa-trash-can"></i> Clear All Notifications
            </button>
          </div>
        </div>
      </div>

      

      {{-- Mood changing --}}
      <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fa-solid fa-moon"></i>
      </button>

      {{-- Profile pic --}}
      <div class="profile-wrapper" id="profileBtn">

        <div class="profile-info" style="display:flex;align-items:center;cursor:pointer;gap:5px;">


          <div
            style="width:40px;height:40px;min-width:40px;overflow:hidden;border-radius:50%;border:2px solid var(--primary);background:#2A3038;display:flex;align-items:center;justify-content:center;">

            <img
              src="{{ auth()->user()->profile_photo_path ? (str_starts_with(auth()->user()->profile_photo_path, 'uploads/') ? asset(auth()->user()->profile_photo_path) : asset('uploads/' . auth()->user()->profile_photo_path)) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->username).'&background=3131ff&color=fff' }}"
              alt="profile" style="width:100%;height:100%;object-fit:cover;display:block;">

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


  <script>
    window.userId = {{ auth()->id() ?? 'null' }};

    // Notification dropdown toggle
    function toggleNotifDropdown(event) {
      if (event) event.stopPropagation();
      document.getElementById('notifDropdown').classList.toggle('show');
    }

    document.addEventListener('click', function (e) {
      const dropdown = document.getElementById('notifDropdown');
      const button = document.getElementById('notifBtn');
      if (dropdown && !dropdown.contains(e.target) && !button.contains(e.target)) {
        dropdown.classList.remove('show');
      }
    });

    function timeSince(date) {
      const seconds = Math.floor((new Date() - date) / 1000);
      let interval = seconds / 31536000;
      if (interval > 1) return Math.floor(interval) + " years ago";
      interval = seconds / 2592000;
      if (interval > 1) return Math.floor(interval) + " months ago";
      interval = seconds / 86400;
      if (interval > 1) return Math.floor(interval) + " days ago";
      interval = seconds / 3600;
      if (interval > 1) return Math.floor(interval) + " hours ago";
      interval = seconds / 60;
      if (interval > 1) return Math.floor(interval) + " minutes ago";
      return "Just now";
    }

    function updateNotifTimes() {
      document.querySelectorAll('.notif-time').forEach(el => {
        const timestamp = el.getAttribute('data-timestamp');
        if (timestamp) el.innerText = timeSince(new Date(timestamp));
      });
    }
    setInterval(updateNotifTimes, 60000);

    // Real-time notification listener — Database + AJAX Polling (every 30 seconds)
    const displayedNotifIds = new Set();
    document.querySelectorAll('#notifDropdown .notif-item').forEach(el => {
      const id = el.getAttribute('data-id');
      if (id) displayedNotifIds.add(id);
    });

    function pollNotifications() {
      if (!window.userId) return;
      fetch('/notifications/poll', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
      })
      .then(notifications => {
        const dropdown = document.getElementById('notifDropdown');
        const countBadge = document.querySelector('.notif-count');
        const iconWrapper = document.querySelector('.notification-icon');
        if (!dropdown) return;
        const notifBody = dropdown.querySelector('.notif-body');
        if (!notifBody) return;

        const serverIds = new Set(notifications.map(n => n.id));

        // 1. Remove notifications marked read/deleted in other tabs
        notifBody.querySelectorAll('.notif-item').forEach(el => {
          const id = el.getAttribute('data-id');
          if (id && !serverIds.has(id)) {
            el.remove();
            displayedNotifIds.delete(id);
          }
        });

        // 2. Add new notifications and trigger toast (prepend newest first)
        for (let i = notifications.length - 1; i >= 0; i--) {
          const notification = notifications[i];
          if (!displayedNotifIds.has(notification.id)) {
            displayedNotifIds.add(notification.id);
            const noNotif = dropdown.querySelector('.no-notif');
            if (noNotif) noNotif.remove();

            const newNotifHtml = `
              <a href="${notification.url}" class="notif-item unread animate__animated animate__fadeInDown" data-id="${notification.id}">
                <div class="notif-title">${notification.title}</div>
                <div class="notif-msg">${notification.message_html}</div>
                <div class="notif-time" data-timestamp="${notification.created_at}">
                  <i class="fa-regular fa-clock"></i> Just now
                </div>
              </a>
            `;
            notifBody.insertAdjacentHTML('afterbegin', newNotifHtml);

            if (typeof Swal !== 'undefined') {
              Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: notification.title || 'New Notification',
                showConfirmButton: false,
                timer: 3000
              });
            }
          }
        }

        // 3. Update Badge Count
        const count = notifications.length;
        if (count > 0) {
          if (countBadge) {
            countBadge.innerText = count;
          } else if (iconWrapper) {
            const badge = document.createElement('span');
            badge.className = 'notif-count';
            badge.innerText = count;
            iconWrapper.appendChild(badge);
          }
        } else {
          if (countBadge) countBadge.remove();
          if (!notifBody.querySelector('.notif-item') && !dropdown.querySelector('.no-notif')) {
            notifBody.innerHTML = `
              <div class="no-notif">
                <i class="fa-solid fa-bell-slash"></i>
                <p>No new notifications</p>
              </div>
            `;
          }
        }
      })
      .catch(err => console.error('Error polling notifications:', err));
    }

    // Run immediately and poll every 30 seconds
    if (window.userId) {
      pollNotifications();
      setInterval(pollNotifications, 30000);
    }

    function deleteAllNotifications(event) {
      if (event) event.stopPropagation();

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Delete All Notifications?',
          text: 'Are you sure you want to delete all your notifications?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc2626',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, Delete All',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            performDeleteAllNotifs();
          }
        });
      } else if (confirm('Are you sure you want to delete all notifications?')) {
        performDeleteAllNotifs();
      }
    }

    function performDeleteAllNotifs() {
      fetch("{{ route('notifications.clearAll') }}", {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const countBadge = document.querySelector('.notif-count');
          if (countBadge) countBadge.remove();

          const notifBody = document.querySelector('.notif-body');
          if (notifBody) {
            notifBody.innerHTML = `
              <div class="no-notif">
                <i class="fa-solid fa-bell-slash"></i>
                <p>No new notifications</p>
              </div>
            `;
          }
          if (typeof displayedNotifIds !== 'undefined') {
            displayedNotifIds.clear();
          }

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'All notifications deleted',
              showConfirmButton: false,
              timer: 2500
            });
          }
        }
      })
      .catch(err => console.error('Error clearing notifications:', err));
    }
  </script>
  <x-calculator />
  @stack('scripts')
</body>

</html>