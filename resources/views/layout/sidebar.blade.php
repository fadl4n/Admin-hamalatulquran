<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link text-center">
        <span class="brand-text font-weight-bold text-white">CMS</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @if (!empty($sidebarMenus))
                    @foreach ($sidebarMenus as $menu)
                        <li class="nav-item has-treeview {{ collect($menu['menuItem'])->contains(fn($item) => request()->is($item['url'])) ? 'menu-open' : '' }}">
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
                                        <a href="{{ url($item['url']) }}" class="nav-link {{ request()->is($item['url']) ? 'active' : '' }}">
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
/* Pastikan sidebar dapat di-scroll dan menyesuaikan tinggi */
.main-sidebar {
    overflow-y: auto; /* Sidebar bisa di-scroll */
    height: 200vh; /* Sesuai tinggi layar */
}

/* Efek scroll di Firefox */
.main-sidebar:hover {
    scrollbar-width: thin;
}

/* Custom scrollbar untuk Chrome, Edge, dan Safari */
.main-sidebar::-webkit-scrollbar {
    width: 6px;
}

.main-sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}

/* Hindari masalah saat sidebar disembunyikan */
body.sidebar-collapse .main-sidebar {
    overflow: hidden;
}
</style>
