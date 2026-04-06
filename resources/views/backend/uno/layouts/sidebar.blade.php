<aside class="sidebar">
    <div class="sidebar-header">
        <button class="sidebar-toggle">
            <span class="material-symbols-rounded">chevron_left</span>
        </button>
    </div>

    {{-- @php
        $bn = app()->getLocale() == 'bn';
    @endphp --}}

    <div class="sidebar-content">
        <ul class="menu-list list-unstyled">

            <li class="menu-item">
                <a href="{{ route('uno.dashboard.index') }}"
                    class="menu-link {{ request()->routeIs('uno.dashboard.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>

            {{-- <li class="menu-item">
                <a href="{{ route('dc.reports.index') }}"
                    class="menu-link {{ request()->routeIs('dc.reports.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">description</span>
                    <span class="menu-label">Reports</span>
                </a>
            </li> --}}

            <li class="menu-item">
                <a href="{{ route('uno.stations.index') }}"
                    class="menu-link {{ request()->routeIs('uno.stations.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">local_gas_station</span>
                    <span class="menu-label">Filling Station</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('uno.tag-officer.index') }}"
                    class="menu-link {{ request()->routeIs('uno.tag-officer.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">assignment_ind</span>
                    <span class="menu-label">Tag Officer</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('uno.assign-tag-officer.index') }}"
                    class="menu-link {{ request()->routeIs('uno.assign-tag-officer.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">assignment_turned_in</span>
                    <span class="menu-label">Assign Tag Officer</span>
                </a>
            </li>


            <li class="menu-item">
                <a href="{{ route('uno.profile.index') }}"
                    class="menu-link {{ request()->routeIs('uno.profile.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">person</span>
                    <span class="menu-label">Profile</span>
                </a>
            </li>





        </ul>
    </div>

    <div class="sidebar-footer">
        <form method="post" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit" class="menu-link logout-link" id="logout-btn">
                <span class="material-symbols-rounded">power_settings_new</span>
                <span class="menu-label" id="logout-text">Logout</span>
            </button>
        </form>
    </div>
</aside>
