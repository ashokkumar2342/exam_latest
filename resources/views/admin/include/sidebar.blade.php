<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link logo-switch text-center">
        <strong>EAGESKOOL EXAM</strong> 
    </a>
    <div class="sidebar">
        <div class="mt-2">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a> 
                    </li> 
                </ul>
            </nav>
        </div>
        @php
        $menuTypes=App\Helper\MyFuncs::userHasMinu();
        @endphp
        @foreach ($menuTypes as $menuType)
        @php
        $subMenus = App\Helper\MyFuncs::mainMenu($menuType->id);
        @endphp
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="{{ $menuType->icon }}"></i>
                        <p>
                            {{ $menuType->name }}
                            <i class="fas fa-angle-left right"></i> 
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach ($subMenus as $subMenu)
                        <li class="nav-item">
                            <a href="{{ route(''.$subMenu->url) }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ $subMenu->name }}</p>
                            </a>
                        </li>
                        @endforeach 
                    </ul>
                </li>
                @endforeach 
            </ul>
        </nav>
    </div>
</aside>



