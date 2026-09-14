@php
    $navbarCategories = \App\Models\Category::orderBy('name', 'asc')->take(10)->get();
@endphp

<header class="main-header">
  <!-- ================= DESKTOP: TOP HEADER ROW ================= -->
  <div class="header-top">
    <div class="container header-top-container">
      
      <!-- Brand Logo -->
      <a class="header-brand" href="{{ route('home-page') }}">
        <img src="{{ asset('image/relectric-logo.png') }}" alt="R Electric">
      </a>

      <!-- Search Bar Pill -->
      <div class="header-search">
        <form action="{{ route('home-page') }}" method="GET" class="search-form">
          <input type="text" name="search" class="search-input" placeholder="I'm shopping for..." value="{{ request('search') }}" autocomplete="off">
          <button type="submit" class="search-btn">
            <i class="fas fa-search"></i>
            <span>Search</span>
          </button>
        </form>
      </div>

      <!-- Right Actions Group -->
      <div class="header-actions">
        <!-- Theme Toggle -->
        <button type="button" class="header-action-icon" onclick="toggleTheme()" title="Toggle Dark/Light Mode" aria-label="Toggle Theme">
          <i class="fas fa-moon theme-toggle-icon"></i>
        </button>

        <!-- User / Profile Dropdown -->
        @auth
          <div class="header-user-dropdown-wrap" id="headerUserDropdownWrap">
            <div class="header-user-btn" id="headerUserBtn" title="My Account" tabindex="0" role="button">
              <div class="header-user-avatar">
                <img
                  src="{{ auth()->user()->profile_photo_path ? (str_starts_with(auth()->user()->profile_photo_path, 'uploads/') ? asset(auth()->user()->profile_photo_path) : asset('uploads/' . auth()->user()->profile_photo_path)) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->username ?? auth()->user()->name ?? 'User').'&background=0202e2&color=fff' }}"
                  alt="{{ auth()->user()->username ?? 'User' }}"
                  class="user-avatar-img">
              </div>
              <div class="header-user-text">
                <span class="sub">Welcome,</span>
                <strong class="main">{{ auth()->user()->username ?? auth()->user()->fullname }}</strong>
              </div>
              <i class="fas fa-chevron-down user-chevron"></i>
            </div>

            <!-- Smart Dropdown Menu -->
            <div class="header-user-dropdown-menu" id="headerUserDropdown">
              <ul class="dropdown-links-list">
                <li>
                  <a class="dropdown-link" href="{{ route('dashboards') }}">
                    <i class="fas fa-gauge-high"></i>
                    <span>Dashboard</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-link" href="{{ route('profile.index') }}">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-link" href="{{ route('settings') }}">
                    <i class="fas fa-gear"></i>
                    <span>settings</span>
                  </a>
                </li>
                <li class="dropdown-divider"></li>
                <li class="dropdown-link-item">
                  <form method="POST" action="{{ route('logout') }}" class="dropdown-logout-form">
                    @csrf
                    <button type="submit" class="dropdown-link dropdown-logout-btn">
                      <i class="fas fa-right-from-bracket"></i>
                      <span>Logout</span>
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        @else
          <a href="javascript:void(0)" onclick="openLoginModal(event)" class="header-user-btn" title="Sign In">
            <div class="header-user-icon">
              <i class="far fa-user"></i>
            </div>
            <div class="header-user-text">
              <span class="sub">Welcome</span>
              <strong class="main">Sign In</strong>
            </div>
          </a>
        @endauth

        <!-- Wishlist Widget Pill -->
        <a href="javascript:void(0)" class="header-pill-widget" title="Wishlist">
          <div class="widget-icon-wrap">
            <i class="far fa-heart icon-heart"></i>
            <span class="widget-badge">0</span>
          </div>
          <div class="widget-text">
            <span class="sub">Wishlist</span>
            <strong class="main">Items</strong>
          </div>
        </a>

        <!-- Cart Widget Pill -->
        <a href="javascript:void(0)" class="header-pill-widget" title="Your Cart" onclick="openCartModal()">
          <div class="widget-icon-wrap">
            <i class="fas fa-basket-shopping icon-cart"></i>
            <span class="widget-badge cart-count-badge" style="display: none;">0</span>
          </div>
          <div class="widget-text">
            <span class="sub">Your Cart</span>
            <strong class="main cart-total-text">৳0.00</strong>
          </div>
        </a>
      </div>

    </div>
  </div>

  <!-- ================= DESKTOP: BOTTOM NAV ROW ================= -->
  <div class="header-nav">
    <div class="container header-nav-container">

      <!-- Browse Categories Button & Dropdown -->
      <div class="categories-btn-wrapper" id="categoriesDropdownWrap">
        <button type="button" class="browse-categories-btn" id="browseCatBtn">
          <span class="btn-left">
            <i class="fas fa-bars"></i>
            <span>BROWSE CATEGORIES</span>
          </span>
          <i class="fas fa-chevron-down"></i>
        </button>

        <ul class="categories-dropdown-menu">
          @forelse($navbarCategories as $cat)
            <li>
              <a href="{{ route('home-page') }}?category={{ $cat->id }}">
                <span>{{ $cat->name }}</span>
                <i class="fas fa-chevron-right"></i>
              </a>
            </li>
          @empty
            <li><a href="#"><span>Lighting Solutions</span><i class="fas fa-chevron-right"></i></a></li>
            <li><a href="#"><span>Switches & Plugs</span><i class="fas fa-chevron-right"></i></a></li>
            <li><a href="#"><span>Industrial Wiring</span><i class="fas fa-chevron-right"></i></a></li>
            <li><a href="#"><span>Professional Tools</span><i class="fas fa-chevron-right"></i></a></li>
          @endforelse
        </ul>
      </div>

      <!-- Main Navigation Menu Links -->
      <ul class="nav-menu-links">
        <li>
          <a href="{{ route('home-page') }}" class="{{ request()->routeIs('home-page') && !request('category') && !request('search') ? 'active' : '' }}">Home</a>
        </li>
        <li>
          <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About Us</a>
        </li>
        <li>
          <a href="{{ route('home-page') }}#products">Shop</a>
        </li>
        <li>
          <a href="{{ route('home-page') }}#best-sellers">Best Sellers</a>
        </li>
        <li>
          <a href="{{ route('home-page') }}#deals">Today's Deals</a>
        </li>
        <li>
          <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact Us</a>
        </li>
      </ul>

      <!-- Special Offers Link -->
      <a href="{{ route('home-page') }}#offers" class="special-offers-link">
        <i class="fas fa-percent"></i>
        <span>Special Offers!</span>
      </a>

    </div>
  </div>

  <!-- ================= MOBILE HEADER BAR ================= -->
  <div class="mobile-header-bar">
    <a class="brand" href="{{ route('home-page') }}">
      <img src="{{ asset('image/relectric-logo.png') }}" alt="R Electric">
    </a>

    <div class="mobile-actions-group">
      <!-- Search Toggle -->
      <button type="button" class="mobile-icon-btn" onclick="toggleMobileSearch()" aria-label="Search" title="Search">
        <i class="fas fa-search"></i>
      </button>

      <!-- Theme Switcher -->
      <button type="button" class="mobile-icon-btn" onclick="toggleTheme()" aria-label="Theme" title="Toggle Theme">
        <i class="fas fa-moon theme-toggle-icon"></i>
      </button>

      <!-- User Profile / Login -->
      @auth
        <div class="header-user-dropdown-wrap mobile-user-wrap" id="mobileUserDropdownWrap">
          <button type="button" class="mobile-icon-btn mobile-avatar-btn" id="mobileUserBtn" title="My Account" onclick="toggleMobileUserDropdown(event)" aria-label="User Menu">
            <img
              src="{{ auth()->user()->profile_photo_path ? (str_starts_with(auth()->user()->profile_photo_path, 'uploads/') ? asset(auth()->user()->profile_photo_path) : asset('uploads/' . auth()->user()->profile_photo_path)) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->username ?? auth()->user()->name ?? 'User').'&background=0202e2&color=fff' }}"
              alt="Avatar"
              style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--primary); display: block;">
          </button>
          <div class="header-user-dropdown-menu mobile-user-dropdown-menu" id="mobileUserDropdown">
            <ul class="dropdown-links-list">
              <li>
                <a class="dropdown-link" href="{{ route('dashboards') }}">
                  <i class="fas fa-gauge-high"></i>
                  <span>Dashboard</span>
                </a>
              </li>
              <li>
                <a class="dropdown-link" href="{{ route('profile.index') }}">
                  <i class="fas fa-user"></i>
                  <span>Profile</span>
                </a>
              </li>
              <li>
                <a class="dropdown-link" href="{{ route('settings') }}">
                  <i class="fas fa-gear"></i>
                  <span>settings</span>
                </a>
              </li>
              <li class="dropdown-divider"></li>
              <li class="dropdown-link-item">
                <form method="POST" action="{{ route('logout') }}" class="dropdown-logout-form">
                  @csrf
                  <button type="submit" class="dropdown-link dropdown-logout-btn">
                    <i class="fas fa-right-from-bracket"></i>
                    <span>Logout</span>
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      @else
        <a href="javascript:void(0)" onclick="openLoginModal(event)" class="mobile-icon-btn" title="Sign In">
          <i class="far fa-user"></i>
        </a>
      @endauth

      <!-- Wishlist with Badge -->
      <a href="javascript:void(0)" class="mobile-icon-btn" title="Wishlist">
        <div class="widget-icon-wrap">
          <i class="far fa-heart icon-heart" style="font-size: 20px;"></i>
          <span class="widget-badge">0</span>
        </div>
      </a>

      <!-- Cart with Badge -->
      <a href="javascript:void(0)" class="mobile-icon-btn" title="Cart" onclick="openCartModal()">
        <div class="widget-icon-wrap">
          <i class="fas fa-basket-shopping icon-cart" style="font-size: 19px;"></i>
          <span class="widget-badge cart-count-badge" style="display: none;">0</span>
        </div>
      </a>

      <!-- Hamburger Menu Toggle Button (Soft square pill) -->
      <button type="button" class="mobile-icon-btn btn-menu-toggle" onclick="toggleSidebar()" aria-label="Toggle Menu" title="Menu">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Collapsible Search Container -->
  <div class="mobile-search-drawer" id="mobileSearchDrawer">
    <form action="{{ route('home-page') }}" method="GET" class="search-form">
      <input type="text" name="search" class="search-input" placeholder="I'm shopping for..." value="{{ request('search') }}" autocomplete="off">
      <button type="submit" class="search-btn">
        <i class="fas fa-search"></i>
        <span>Search</span>
      </button>
    </form>
  </div>
</header>

<!-- ================= MOBILE SIDEBAR / OFFCANVAS ================= -->
<div id="mobileSidebar">
  <div class="mobile-sidebar-header">
    <a class="brand" href="{{ route('home-page') }}">
      <img src="{{ asset('image/relectric-logo.png') }}" alt="R Electric" style="height: 30px;">
    </a>
    <button type="button" class="close-btn" onclick="toggleSidebar()">&times;</button>
  </div>

  <div class="mobile-sidebar-body">
    <!-- Categories Accordion -->
    <div class="mobile-cat-accordion" id="mobileCatAccordion">
      <div class="cat-toggle" onclick="toggleMobileCategory()">
        <span><i class="fas fa-bars me-2"></i> BROWSE CATEGORIES</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      <div class="mobile-cat-list">
        @forelse($navbarCategories as $cat)
          <a href="{{ route('home-page') }}?category={{ $cat->id }}" class="mobile-cat-item">
            {{ $cat->name }}
          </a>
        @empty
          <a href="#" class="mobile-cat-item">Lighting Solutions</a>
          <a href="#" class="mobile-cat-item">Switches & Plugs</a>
          <a href="#" class="mobile-cat-item">Industrial Wiring</a>
          <a href="#" class="mobile-cat-item">Professional Tools</a>
        @endforelse
      </div>
    </div>

    <!-- Navigation Links -->
    <a href="{{ route('home-page') }}" class="mobile-nav-link {{ request()->routeIs('home-page') ? 'active' : '' }}">
      <span><i class="fas fa-house me-2"></i> Home</span>
    </a>
    <a href="{{ route('about') }}" class="mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
      <span><i class="fas fa-circle-info me-2"></i> About Us</span>
    </a>
    <a href="{{ route('home-page') }}#products" class="mobile-nav-link">
      <span><i class="fas fa-bag-shopping me-2"></i> Shop</span>
    </a>
    <a href="{{ route('home-page') }}#best-sellers" class="mobile-nav-link">
      <span><i class="fas fa-fire me-2"></i> Best Sellers</span>
    </a>
    <a href="{{ route('home-page') }}#deals" class="mobile-nav-link">
      <span><i class="fas fa-tags me-2"></i> Today's Deals</span>
    </a>
    <a href="{{ route('contact') }}" class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">
      <span><i class="fas fa-headset me-2"></i> Contact Us</span>
    </a>
    <a href="{{ route('home-page') }}#offers" class="mobile-nav-link" style="color: var(--accent);">
      <span><i class="fas fa-percent me-2"></i> Special Offers!</span>
    </a>
  </div>

  <div class="mobile-sidebar-footer">
    @auth
      <a href="{{ route('dashboards') }}" class="btn btn-sm btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
        <i class="fas fa-gauge-high"></i> Dashboard
      </a>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-2">
          <i class="fas fa-right-from-bracket me-1"></i> Logout
        </button>
      </form>
    @else
      <button type="button" class="btn btn-sm btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2" onclick="toggleSidebar(); openLoginModal(event);">
        <i class="fas fa-user-circle"></i> Sign In / Register
      </button>
    @endauth
  </div>
</div>

<div id="sidebarOverlay" onclick="toggleSidebar()"></div>