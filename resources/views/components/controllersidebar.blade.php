<nav class="sidebar">
  <ul>
    <li class="first">
      <span><a class="nav-logo" style="border: none " href="{{ route('dashboards') }}">RunStar</a></span>

      <button class="toggle-btn" id="toggleSide" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 -960 960 960" width="26px" fill="#220901">
          <path
            d="M440-240 200-480l240-240 56 56-183 184 183 184-56 56Zm264 0L464-480l240-240 56 56-183 184 183 184-56 56Z" />
        </svg>
      </button>
    </li>
    <li>
      <a href="{{ route('dashboards') }}" title="Dashboard">
        <img src="{{asset('svg/dashboard.svg')}}" alt="Dashboard" />
        <span>Dashboard</span>
      </a>
    </li>

    <li>
      <a href="{{ route('dashboards') }}" title="Dashboard">
        <img src="{{asset('svg/dashboard.svg')}}" alt="Dashboard" />
        <span>Book Now</span>
      </a>
    </li>

    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(event)" title="location" id="drop">
        <img src="{{asset('svg/driver.svg')}}" />
        <span>Drivers</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
          fill="#0000F5">
          <path d="M480-360 280-560h400L480-360Z" />
        </svg>
      </a>
      <ul class="submenu">
        <div>
          <li><a href="{{ route('admin.drivers.create') }}" title="Add location" id="sub">
              <img src="{{asset('svg/driver.svg')}}" />
              <span>Add Driver</span>
            </a>
          </li>
          <li><a href="{{ route('admin.drivers.index') }}" title="All locations" id="sub">
              <img src="{{asset('svg/list.svg')}}" />
              <span>All Drivers</span>
            </a>
          </li>
        </div>
      </ul>
    </li>

    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(event)" title="location" id="drop">
        <img src="{{asset('svg/supervisor.svg')}}" />
        <span>Supervisor</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
          fill="#0000F5">
          <path d="M480-360 280-560h400L480-360Z" />
        </svg>
      </a>
      <ul class="submenu">
        <div>
          <li><a href="{{ route('admin.supervisors.create') }}" title="Add location" id="sub">
              <img src="{{asset('svg/supervisor.svg')}}" />
              <span>Add Supervisor</span>
            </a>
          </li>
          <li><a href="{{ route('admin.supervisors.index') }}" title="All locations" id="sub">
              <img src="{{asset('svg/list.svg')}}" />
              <span>All Supervisors</span>
            </a>
          </li>
        </div>
      </ul>
    </li>

    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(event)" title="Stations" id="drop">
        <img src="{{asset('svg/schedule.svg')}}" />
        <span>Schedule</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
          fill="#0000F5">
          <path d="M480-360 280-560h400L480-360Z" />
        </svg>
      </a>
      <ul class="submenu">
        <div>
          <li class="{{request()->routeIs('admin.schedules.create')?'active':''}}"><a
              href="{{ route('admin.schedules.create') }}" title="Booking" id="sub">
              <img src="{{asset('svg/addschedule.svg')}}" />
              <span>Create shedule</span>
            </a>
          </li>
          <li><a href="{{ route('admin.schedules.index') }}" title="All schedules" id="sub">
              <img src="{{asset('svg/list.svg')}}" />
              <span>All schedules</span>
            </a>
          </li>
        </div>
      </ul>
    </li>


    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(event)" title="Stations" id="drop">
        <img src="{{asset('svg/trip.svg')}}" />
        <span>Trips</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
          fill="#0000F5">
          <path d="M480-360 280-560h400L480-360Z" />
        </svg>
      </a>
      <ul class="submenu">
        <div>
          <li class="{{request()->routeIs('admin.schedules.create')?'active':''}}"><a
              href="{{ route('admin.pendingtrip') }}" title="Booking" id="sub">
              <img src="{{asset('svg/pending.svg')}}" />
              <span>Pending Trips</span>
            </a>
          </li>
          <li><a href="{{ route('admin.runningtrip') }}" title="All schedules" id="sub">
              <img src="{{asset('svg/running.svg')}}" />
              <span>Running Trips</span>
            </a>
          </li>
          <li><a href="{{ route('admin.finishedtrip') }}" title="All schedules" id="sub">
              <img src="{{asset('svg/finish.svg')}}" />
              <span>Finished Trips</span>
            </a>
          </li>
        </div>
      </ul>
    </li>

    <li>
      <a href="{{ route('admin.reservation.index') }}" title="Dashboard">
        <img src="{{asset('svg/booking.svg')}}" alt="Dashboard" />
        <span>Bookings</span>
      </a>
    </li>

    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(event)" title="location" id="drop">
        <img src="{{asset('svg/location.svg')}}" />
        <span>location</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
          fill="#0000F5">
          <path d="M480-360 280-560h400L480-360Z" />
        </svg>
      </a>
      <ul class="submenu">
        <div>
          <li><a href="{{ route('admin.locations.create') }}" title="Add location" id="sub">
              <img src="{{asset('svg/addlocation.svg')}}" />
              <span>Add location</span>
            </a>
          </li>
          <li><a href="{{ route('admin.locations.index') }}" title="All locations" id="sub">
              <img src="{{asset('svg/list.svg')}}" />
              <span>All locations</span>
            </a>
          </li>
        </div>
      </ul>
    </li>



    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(event)" title="route" id="drop">
        <img src="{{asset('svg/route.svg')}}" />
        <span>Route</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
          fill="#0000F5">
          <path d="M480-360 280-560h400L480-360Z" />
        </svg>
      </a>
      <ul class="submenu">
        <div>
          <li><a href="{{ route('admin.routes.create') }}" title="Add route" id="sub">
              <img src="{{asset('svg/addroute.svg')}}" />
              <span>Add Route</span>
            </a>
          </li>
          <li><a href="{{ route('admin.routes.index') }}" title="All routes" id="sub">
              <img src="{{asset('svg/list.svg')}}" />
              <span>All Routes</span>
            </a>
          </li>
        </div>
      </ul>
    </li>


    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(event)" title="Stations" id="drop">
        <img src="{{asset('svg/schedule.svg')}}" />
        <span>Cost</span>
        <svg class="arrow" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
          fill="#0000F5">
          <path d="M480-360 280-560h400L480-360Z" />
        </svg>
      </a>
      <ul class="submenu">
        <div>
          <li class="{{request()->routeIs('admin.schedules.create')?'active':''}}"><a
              href="{{ route('admin.costs.create') }}" title="Booking" id="sub">
              <img src="{{asset('svg/addschedule.svg')}}" />
              <span>Add Cost</span>
            </a>
          </li>
          <li><a href="{{ route('admin.costs.index') }}" title="All schedules" id="sub">
              <img src="{{asset('svg/list.svg')}}" />
              <span>All Costs</span>
            </a>
          </li>
        </div>
      </ul>
    </li>

    <li>
      <a href="#" title="Caleder">
        <img src="{{asset('svg/schedule.svg')}}" />
        <span>Calender</span>
      </a>
    </li>
  </ul>
</nav>