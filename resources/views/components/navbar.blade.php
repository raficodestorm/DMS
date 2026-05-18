
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">

    <a class="brand" href="{{ route('home-page') }}">
      <img src="{{ asset('image/relectric-logo.png') }}" class="img-logo img-fluid" id="img-logo" alt="relectric" >
    </a>


    <div class="collapse navbar-collapse d-none d-lg-flex">

      <ul class="navbar-nav ms-auto text-center">
        <!-- <i class="fas fa-moon theme-toggle" onclick="toggleTheme()" id="themeIcon"></i> -->
        <li class="nav-item">
          <a class="nav-link  px-3" href="{{ route('home-page') }}">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link  px-3" href="{{ route('about') }}">About</a>
        </li>

        <li class="nav-item dropdown-custom">
          <a class="nav-link px-3 dropdown-toggle-custom" href="javascript:void(0)">
            Categories
          </a>

          <ul class="dropdown-menu-custom">
            <li><a href="#">Lighting Solutions</a></li>
            <li><a href="#">Switches & Plugs</a></li>
            <li><a href="#">Industrial Wiring</a></li>
            <li><a href="#">Professional Tools</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link  px-3" href="{{ route('contact') }}">Contact</a>
        </li>
        <li class="nav-item">
          <a class="btn-login" href="{{ route('login') }}">
            <i class="fas fa-user-circle" style="font-size: 32px; color: var(--primary);"></i>
          </a>
        </li>
      </ul>
    </div>

    <div id="mobileSidebar">
      <span class="close-btn" onclick="toggleSidebar()">&times;</span>

      <div class="mobile-menu">
        <!-- <div class="mood d-flex justify-content-center">
          <i class="fas fa-moon theme-toggle" onclick="toggleTheme()" id="themeIcon"></i>
        </div> -->
        <a href="{{ route('home-page') }}">Home</a>
        <a href="{{ route('about') }}">About</a>
        <div class=" mobile-dropdown">
          <div class="mobile-dropdown-toggle">
            Categories <i class="fas fa-chevron-down"></i>
          </div>
          <div class="dropdown-menu-custom">
                        <a href="#">Lighting Solutions</a>
                        <a href="#">Switches & Plugs</a>
                        <a href="#">Industrial Wiring</a>
                        <a href="#">Professional Tools</a>
                    </div>
        </div>
        <a href="{{ route('contact') }}">Contact</a>

      </div>


      <div class="sidebar-footer">
    <a class="btn-login" href="{{ route('login') }}">
        <i class="fas fa-user-circle"
           style="font-size: 32px; color: var(--primary);"></i>
    </a>
</div>
    </div>
    <button class="navbar-toggler" type="button" onclick="toggleSidebar()" style="border-color: var(--primary);">
      <span class="fa-solid fa-bars" style="color: var(--primary);"></span>
    </button>

    <div id="sidebarOverlay" onclick="toggleSidebar()"></div>


  </div>
</nav>

        