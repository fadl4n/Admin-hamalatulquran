<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 100vh; overflow-y: auto;">

  <!-- Brand Logo -->
  <a href="{{url('/')}}" class="brand-link text-center">
    {{-- <img src="{{ asset ("/bower_components/admin-lte/dist/img/AdminLTELogo.png") }}" alt="AdminLTE Logo"
      class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
    <span class="brand-text font-weight-bold text-center text-white">CMS</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
          with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="{{url('/')}}" class="{{ (request()->is('/')) ? 'nav-link active' : 'nav-link' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>
        @if (isset($sidebarMenus))
          @foreach ($sidebarMenus as $menu)
            <li class="nav-item {{ areActiveRoutes($menu['menuItem']) }}">
              <a href="#" class="nav-link">
                <i class="nav-icon {{ $menu['icon'] }}"></i>
                <p>
                  {{ $menu['name'] }}
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @foreach ($menu['menuItem'] as $item)
                <li class="nav-item">
                  <a href="{{ url($item['url']) }}" class="nav-link {{ strpos(Request::url(), $item['url']) ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ $item['name'] }}</p>
                  </a>
                </li>
                @endforeach
              </ul>
            </li>
          @endforeach
        @endif

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
