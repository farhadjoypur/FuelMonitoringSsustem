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
                <a href="{{ route('tag-officer.dashboard.index') }}"
                    class="menu-link {{ request()->routeIs('user.dashboard.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">dashboard</span>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="{{ route('fuel-reports.index') }}"
                    class="menu-link {{ request()->routeIs('tag-officer.sales-report.*') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">analytics</span>
                    <span class="menu-label">Sales Report</span>
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
