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
  <title>{{ config('app.name') }} | Customer panel</title>
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

      <li class="nav-item">
        <a href="{{ route('customer.payments.index') }}" class="nav-link {{ isActive('customer.payments.index') }}">
          <i class="fas fa-money-bill-transfer"></i> Transactions
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('customer.orders.index') }}" class="nav-link {{ isActive('customer.orders.index') }}">
          <i class="fas fa-money-bill-transfer"></i> Orders
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



      <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fa-solid fa-moon"></i>
      </button>

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