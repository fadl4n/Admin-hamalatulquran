  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="nav-item">
            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img src="{{ url(Session::get('user')['image']) }}" alt="user"
                    class="rounded-circle" width="30">
                <span class="ml-2 d-none d-lg-inline-block"><span class="text-dark">{{ Session::get('user')['name'] }}</span>
                    <i data-feather="chevron-down" class="svg-icon"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                <a class="dropdown-item" href="{{ url('/profile') }}"><i data-feather="user"
                    class="svg-icon mr-2 ml-1"></i>
                    My Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ url('/logout') }}"><i data-feather="power"
                        class="svg-icon mr-2 ml-1"></i>
                    Logout</a>
            </div>
          </li>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
