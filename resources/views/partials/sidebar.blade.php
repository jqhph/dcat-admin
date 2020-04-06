<div class="main-menu">
{{--    <div class="navbar-header">--}}
{{--        <ul class="nav navbar-nav flex-row">--}}
{{--            <li class="nav-item mr-auto">--}}
{{--                <a href="{{ admin_url('/') }}" class="navbar-brand waves-effect waves-light">--}}
{{--                    <span class="logo-mini">{!! config('admin.logo-mini') !!}</span>--}}
{{--                    <span class="logo-lg">{!! config('admin.logo') !!}</span>--}}
{{--                </a>--}}

{{--                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">--}}
{{--                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>--}}
{{--                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary collapse-toggle-icon"--}}
{{--                       data-ticon="icon-disc">--}}
{{--                    </i>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--        </ul>--}}
{{--    </div>--}}
{{--    <div class="shadow-bottom"></div>--}}
    <div class="main-menu-content">
        <aside class="main-sidebar sidebar-light-primary shadow">
            <!-- Brand Logo -->
            <a href="index3.html" class="brand-link">
                <img class="brand-image img-circle elevation-3" >
                <span class="brand-text font-weight-light">AdminLTE 3</span>
            </a>

            <div class="sidebar">

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_TOP) !!}

                        {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU) !!}

                        {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_BOTTOM) !!}
                    </ul>
                </nav>
            </div>
        </aside>
    </div>
</div>