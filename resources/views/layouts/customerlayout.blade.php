<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name') }}</title>
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
      <img src="{{ asset('image/relectric-logo.png') }}" alt="Logo" class="sidebar-logo img-fluid" style="width: 170px; height: 60x;">
    </div>
    <ul class="nav-menu">

      <!-- Dashboard -->
      <li class="nav-item">
        <a href="{{ route('dashboards') }}" class="nav-link {{ isActive('dashboards') }}">
          <i class="fas fa-gauge-high"></i> Dashboard
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('customer.payments.index') }}" class="nav-link {{ isActive('customer.payments.index') }}">
          <i class="fas fa-money-bill-transfer"></i> Transactions
        </a>
      </li>

      <!-- Users -->
      {{-- <li class="nav-item">
        <div class="nav-link has-dropdown {{ isActive(['admin.users.*','admin.index.managers']) }}">
          <i class="fas fa-users"></i>
          <span>Users</span>
          <i class="fas fa-chevron-down arrow"></i>
        </div>

        <ul class="sub-menu" style="{{ isOpen(['admin.users.*','admin.index.managers']) }}">
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
        </ul>
      </li> --}}



    </ul>
  </aside>


  <header class="custom-navbar">

    <div class="nav-left">

      <div class="menu-toggle" id="toggleBtn">
        <i style="color:var(--primary);" class="fa-solid fa-bars"></i>
      </div>

      

    </div>


    <div class="nav-right">

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
            <a href="{{ route('notifications.markAndRedirect', $note->id) }}" class="notif-item unread">
              <div class="notif-title">
                {{ $note->data['title'] }}
              </div>
              <div class="notif-msg">
                @if(is_array($note->data['message']))
                {{ $note->data['message']['text'] }}

                @else
                {{ $note->data['message'] }}
                @endif
              </div>
              <div class="notif-time">
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
    window.userId = {{ auth()->id() ?? 'null' }};
    
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
    // Notification dropdown
    function toggleNotifDropdown(event) {

    if(event) event.stopPropagation();
    
    const dropdown = document.getElementById('notifDropdown');
    dropdown.classList.toggle('show');
}

document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('notifDropdown');
    const button = document.getElementById('notifBtn');

    if (dropdown && !dropdown.contains(e.target) && !button.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});
    // RealTime Notification system
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
            if (timestamp) {
                el.innerText = timeSince(new Date(timestamp));
            }
        });
    }

    setInterval(updateNotifTimes, 60000);

    document.addEventListener('DOMContentLoaded', function () {
        if (window.userId && window.Echo) {
            window.Echo.private(`App.Models.User.${window.userId}`)
                .notification((notification) => {
                    
                    let displayMessage = '';
                    let senderInfo = '';
                    if (typeof notification.message === 'object' && notification.message !== null) {
                        displayMessage = notification.message.text || '';
                        senderInfo = notification.message.from ? `from <span class="text-primary fw-bold">${notification.message.from}</span>` : '';
                    } else {
                        displayMessage = notification.message || '';
                    }

                    const dropdown = document.getElementById('notifDropdown');
                    if (dropdown) {
                        const noNotif = dropdown.querySelector('.no-notif');
                        if (noNotif) noNotif.remove();

                        // এখানে বর্তমান সময় স্টোর করা হচ্ছে data-timestamp এ
                        const now = new Date().toISOString();

                        const newNotifHtml = `
                            <a href="/notifications/${notification.id}/mark-as-read" class="notif-item unread animate__animated animate__fadeInDown">
                                <div class="notif-title">${notification.title}</div>
                                <div class="notif-msg">${displayMessage} ${senderInfo}</div>
                                <div class="notif-time" data-timestamp="${now}">Just now</div>
                            </a>
                        `;

                        const header = dropdown.querySelector('.notif-header');
                        header.insertAdjacentHTML('afterend', newNotifHtml);
                    }

                    let countBadge = document.querySelector('.notif-count');
                    let iconWrapper = document.querySelector('.notification-icon');

                    if (countBadge) {
                        countBadge.innerText = parseInt(countBadge.innerText.trim()) + 1;
                    } else if (iconWrapper) {
                        let newBadge = document.createElement('span');
                        newBadge.className = 'notif-count';
                        newBadge.innerText = '1';
                        iconWrapper.appendChild(newBadge);
                    }

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: notification.title,
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
        }
    });
  </script>
  @stack('scripts')
</body>

</html>