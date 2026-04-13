<nav class="navbar sticky-top " id="header">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <form class="d-flex" role="search">
      <input class="form-control " type="search" placeholder="Search" aria-label="Search" />
      <button class="btn bg-white" type="submit">Search</button>
    </form>
    <div class="col-auto ms-auto">
      <h4 class="panel">{{ auth()->user()->role }} panel</h4>
    </div>
    <div class="col-auto ms-auto">
      <div class="position-relative">
        <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 -960 960 960" width="28px" fill="#ff0000"
          class="notification-icon">
          <path
            d="M80-560q0-100 44.5-183.5T244-882l47 64q-60 44-95.5 111T160-560H80Zm720 0q0-80-35.5-147T669-818l47-64q75 55 119.5 138.5T880-560h-80ZM160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160Zm320-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z" />
        </svg>
        <span class="badge bg-danger rounded-pill notification-badge">3</span>
      </div>

      <!-- Profile Dropdown -->
      @auth
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown"
          data-bs-toggle="dropdown" aria-expanded="false">
          <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="User"
            style="width:40px; height:40px; object-fit:cover; border:2px solid #ff0000; border-radius:50%;">
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="#">Dashboard</a></li>
          <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Edit Profile</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item text-danger">Logout</button>
            </form>
          </li>
          @endauth

        </ul>
      </div>
    </div>
  </div>
</nav>