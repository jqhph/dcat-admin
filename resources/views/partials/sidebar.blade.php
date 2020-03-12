<div class="main-menu menu-fixed {{($configData['theme'] === 'light') ? 'menu-light' : 'menu-dark'}} menu-accordion menu-shadow"
        data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto">
                <a href="{{ admin_url('/') }}" class="navbar-brand waves-effect waves-light">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">{!! config('admin.logo', config('admin.name')) !!}</h2>
                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                    <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
                    <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary collapse-toggle-icon"
                       data-ticon="icon-disc">
                    </i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_TOP) !!}

            {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU) !!}

            {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_BOTTOM) !!}
        </ul>
    </div>
</div>