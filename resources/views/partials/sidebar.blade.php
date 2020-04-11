<div class="main-menu">
    <div class="main-menu-content">
        <aside class="main-sidebar sidebar-light-primary shadow">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto">
                        <a href="{{ admin_url('/') }}" class="navbar-brand waves-effect waves-light">
                            <span class="logo-mini">{!! config('admin.logo-mini') !!}</span>
                            <span class="logo-lg">{!! config('admin.logo') !!}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar mt-1 pb-3">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_TOP) !!}

                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU) !!}

                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_BOTTOM) !!}
                </ul>
            </div>
        </aside>
    </div>
</div>