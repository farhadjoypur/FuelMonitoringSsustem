<aside class="sidebar">
    <div class="sidebar-header">
        <button class="sidebar-toggle">
            <span class="material-symbols-rounded">chevron_left</span>
        </button>
    </div>

    <div class="sidebar-content">
        <ul class="menu-list list-unstyled">

            {{-- DASHBOARD --}}
            <li class="menu-item">
                <a href="{{ route('admin.dashboard.index') }}"
                    class="menu-link {{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>



            {{-- COMPANY (SIMPLE MENU) --}}
            <li class="menu-item">
                <a href="{{ route('admin.companies.index') }}"
                    class="menu-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">business</span>
                    <span class="menu-label">Company</span>
                </a>
            </li>

            {{-- DEPOT --}}
            <li class="menu-item">
                <a href="{{ route('admin.depots.index') }}"
                    class="menu-link {{ request()->routeIs('admin.depots.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">group</span>
                    <span class="menu-label">Depot</span>
                </a>
            </li>

            {{-- FILLING STATION (SIMPLE MENU) --}}
            <li class="menu-item">
                <a href="{{ route('admin.stations.index') }}"
                    class="menu-link {{ request()->routeIs('admin.stations.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">local_gas_station</span>
                    <span class="menu-label">Filling Station</span>
                </a>
            </li>

            {{-- DC OFFICER --}}
            <li class="menu-item">
                <a href="{{ route('admin.dc-officer.index') }}"
                    class="menu-link {{ request()->routeIs('admin.dc-officer.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">group</span>
                    <span class="menu-label">DC Officer</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('admin.uno.index') }}"
                    class="menu-link {{ request()->routeIs('admin.uno.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">badge</span>
                    <span class="menu-label">UNO Officer</span>
                </a>
            </li>

            {{-- TAG OFFICER --}}
            <li class="menu-item">
                <a href="{{ route('admin.tag-officer.index') }}"
                    class="menu-link {{ request()->routeIs('admin.tag-officer.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">assignment_ind</span>
                    <span class="menu-label">Tag Officer</span>
                </a>
            </li>

            {{-- ASSIGN TAG OFFICER --}}
            <li class="menu-item">
                <a href="{{ route('admin.assign-tag-officer.index') }}"
                    class="menu-link {{ request()->routeIs('admin.assign-tag-officer.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">assignment_turned_in</span>
                    <span class="menu-label">Assign Tag Officer</span>
                </a>
            </li>

            {{-- Report --}}

            <li class="menu-item">
                <a href="{{ route('admin.reports.index') }}"
                    class="menu-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">bar_chart</span>
                    <span class="menu-label">Reports</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('admin.admin-user.index') }}"
                    class="menu-link {{ request()->routeIs('admin.admin-user.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">admin_panel_settings</span>
                    <span class="menu-label">Admin User</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('admin.profile.index') }}"
                    class="menu-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">person</span>
                    <span class="menu-label">Profile</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- LOGOUT --}}
    <div class="sidebar-footer">
        <form method="post" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit" class="menu-link logout-link" id="logout-btn">
                <span class="material-symbols-rounded">power_settings_new</span>
                <span class="menu-label">Logout</span>
            </button>
        </form>
    </div>
</aside>
