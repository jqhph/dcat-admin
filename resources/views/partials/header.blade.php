<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ admin_url('/') }}" class="logo waves-effect waves-light">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">{!! config('admin.logo-mini', config('admin.name')) !!}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">{!! config('admin.logo', config('admin.name')) !!}</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle  waves-effect waves-70" data-toggle="offcanvas" role="button">
            <span class="sr-only"></span>
        </a>

        {!! Dcat\Admin\Admin::navbar()->render('left') !!}

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                {!! Dcat\Admin\Admin::navbar()->render() !!}

                {{--User Account Menu--}}
                {!! admin_section(AdminSection::NAVBAR_USER_PANEL) !!}

                {{--Control Sidebar Toggle Button--}}
                {{--<li>--}}
                    {{--<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>--}}
                {{--</li>--}}
                {!! admin_section(AdminSection::NAVBAR_AFTER_USER_PANEL) !!}
            </ul>
        </div>
    </nav>
</header>