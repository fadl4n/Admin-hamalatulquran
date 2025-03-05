<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4"
    style="height: 100vh; overflow-y: scroll; background: #343a40; width: 250px; position: fixed; top: 0; left: 0; bottom: 0; scrollbar-width: none;">

    <!-- Brand Logo -->
    <a href="{{url('/')}}" class="brand-link text-center">
      <span class="brand-text font-weight-bold text-center text-white">CMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{url('/')}}" class="{{ (request()->is('/')) ? 'nav-link active' : 'nav-link' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
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
    </div>
</aside>

<style>
/* Hilangkan scrollbar di Chrome, Safari */
.main-sidebar::-webkit-scrollbar {
    display: none;
}

/* Hilangkan scrollbar di Firefox */
.main-sidebar {
    scrollbar-width: none;
}
</style>
