<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">

    <a class="brand" href="index.php">
      {{-- <img src="https://raficon.safiulrafi.top/images/logo.webp" class="img-logo" id="img-logo" alt="RafiCon"> --}}
      <h3 class="top-logo">{{ config('app.name') }}</h3>
    </a>


    <div class="collapse navbar-collapse d-none d-lg-flex">

      <ul class="navbar-nav ms-auto text-center">
        <!-- <i class="fas fa-moon theme-toggle" onclick="toggleTheme()" id="themeIcon"></i> -->
        <li class="nav-item">
          <a class="nav-link  px-3" href="index.php">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link  px-3" href="pages/about.php">About</a>
        </li>

        <li class="nav-item dropdown-custom">
          <a class="nav-link px-3 dropdown-toggle-custom" href="javascript:void(0)">
            Categories
          </a>

          <ul class="dropdown-menu-custom">
            <li><a href="pages/image-converter.php">Cable</a></li>
            <li><a href="pages/bg-remover.php">Electric tools</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link  px-3" href="#contact">Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link  px-3" href="{{ route('login') }}">Login</a>
        </li>
      </ul>
    </div>

    <div id="mobileSidebar">
      <span class="close-btn" onclick="toggleSidebar()">&times;</span>

      <div class="mobile-menu">
        <!-- <div class="mood d-flex justify-content-center">
          <i class="fas fa-moon theme-toggle" onclick="toggleTheme()" id="themeIcon"></i>
        </div> -->
        <a href="index.php">Home</a>
        <a href="pages/about.php">About</a>
        <div class=" mobile-dropdown">
          <div class="mobile-dropdown-toggle">
            Categories <i class="fas fa-chevron-down"></i>
          </div>
          <div class="mobile-submenu">
            <a href="pages/image-converter.php">Cable</a>
            <a href="pages/bg-remover.php">Electric tool</a>
          </div>
        </div>
        <a href="pages/contact.php">Contact</a>

      </div>


      <div class="sidebar-footer">
        <a class="btn-login" href="{{ route('login') }}">Login<i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
    <button class="navbar-toggler" type="button" onclick="toggleSidebar()" style="border-color: var(--primary);">
      <span class="fa-solid fa-bars" style="color: var(--primary);"></span>
    </button>

    <div id="sidebarOverlay" onclick="toggleSidebar()"></div>


  </div>
</nav>