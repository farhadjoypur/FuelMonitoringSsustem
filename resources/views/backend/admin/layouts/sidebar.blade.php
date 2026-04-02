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
                <a href="{{ route('admin.dashboard.index') }}"
                    class="menu-link {{ request()->routeIs('user.dashboard.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>

            {{-- COMPANY MENU (WITH SUBMENU) --}}
            <li class="menu-item has-submenu">

                <a href="javascript:void(0);"
                   class="menu-link submenu-toggle">
                    <span class="material-symbols-rounded">business</span>
                    <span class="menu-label">Company</span>
                    <span class="material-symbols-rounded submenu-icon">expand_more</span>
                </a>

                <ul class="submenu list-unstyled">

                    <li>
                        <a href="{{ route('companies.index') }}"
                           class="submenu-link {{ request()->routeIs('companies.index') ? 'active' : '' }}">
                            Company List
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('companies.create') }}"
                           class="submenu-link {{ request()->routeIs('companies.create') ? 'active' : '' }}">
                            Add Company
                        </a>
                    </li>

                </ul>
            </li>

            {{-- FILLING STATION MENU (WITH SUBMENU) --}}
            <li class="menu-item has-submenu">

                    <a href="javascript:void(0);"
                    class="menu-link submenu-toggle">
                        <span class="material-symbols-rounded">business</span>
                        <span class="menu-label">Filling Station</span>
                        <span class="material-symbols-rounded submenu-icon">expand_more</span>
                    </a>

                    <ul class="submenu list-unstyled">

                        <li>
                            <a href="{{ route('stations.index') }}"
                            class="submenu-link {{ request()->routeIs('stations.index') ? 'active' : '' }}">
                                Filling Station List
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('stations.create') }}"
                            class="submenu-link {{ request()->routeIs('stations.create') ? 'active' : '' }}">
                                Add Filling Station
                            </a>
                        </li>

                    </ul>
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
