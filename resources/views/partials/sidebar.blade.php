<aside class="sidebar">

    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-buildings-fill"></i>
        </div>

        <div class="brand-text">
            <h1>Paris Housing</h1>
            <span>BI Dashboard</span>
        </div>
    </div>
    <nav class="sidebar-menu">

        <a href="{{ route('dashboard') }}"
            class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('location.insights') }}"
            class="menu-item {{ request()->routeIs('location.insights') ? 'active' : '' }}">
            <i class="bi bi-geo-alt-fill"></i>
            <span>Location Insights</span>
        </a>

        <a href="{{ route('property.types') }}"
            class="menu-item {{ request()->routeIs('property.types') ? 'active' : '' }}">
            <i class="bi bi-building-fill"></i>
            <span>Property Types</span>
        </a>

        <a href="{{ route('price.analysis') }}"
            class="menu-item {{ request()->routeIs('price.analysis') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i>
            <span>Price Analysis</span>
        </a>

        <a href="{{ route('property.condition') }}"
            class="menu-item {{ request()->routeIs('property.condition') ? 'active' : '' }}">
            <i class="bi bi-clipboard2-data-fill"></i>
            <span>Property Condition</span>
        </a>

        <a href="{{ route('property.data') }}"
            class="menu-item {{ request()->routeIs('property.data') ? 'active' : '' }}">
            <i class="bi bi-database-fill"></i>
            <span>Property Data</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>