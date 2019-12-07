<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ Dcat\Admin\Admin::title() }} @if($header) | {{ $header }}@endif</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    @if(!config('admin.disable_no_referrer_meta'))
    <meta name="referrer" content="no-referrer"/>
    @endif

    {!! admin_section(\AdminSection::HEAD) !!}

    {!! Dcat\Admin\Admin::css() !!}

    <script src="{{ Dcat\Admin\Admin::jQuery() }}"></script>
    {!! Dcat\Admin\Admin::headerJs() !!}

    @if(!empty($favicon = Dcat\Admin\Admin::favicon()))
        <link rel="shortcut icon" href="{{$favicon}}">
    @endif

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="dcat-admin-body hold-transition {{config('admin.skin')}} {{implode(' ', config('admin.layout'))}}">
    {!! admin_section(\AdminSection::BODY_INNER_BEFORE) !!}

    @include('admin::partials.script')

    <div class="wrapper">
        @include('admin::partials.header')

        <aside class="main-sidebar">
            <section class="sidebar">

                {!! admin_section(AdminSection::LEFT_SIDEBAR_USER_PANEL) !!}

                <ul class="sidebar-menu">
                    <li class="header">{{ trans('admin.menu') }}</li>

                    {!! admin_section(\AdminSection::LEFT_SIDEBAR_MENU_TOP) !!}

                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU) !!}

                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_BOTTOM) !!}

                </ul>
            </section>
        </aside>

        <div class="content-wrapper" id="pjax-container" style="min-height:1500px">
            @yield('app')
        </div>

        @if(config('admin.go_to_top_btn') !== false)
            <div class="fixed-bottom-btn"><a class="waves-effect waves-light" id="go-top"><i class="ti-angle-up"></i></a></div>
        @endif

        <footer class="main-footer">
            <div class="text-center text-80 font-12">
                Powered by
                <a target="_blank" href="https://github.com/jqhph/dcat-admin">Dcat Admin</a>
                <span>&nbsp;·&nbsp;</span>
                {{ Dcat\Admin\Admin::VERSION }}
            </div>
        </footer>

        <aside class="control-sidebar {!! admin_section(AdminSection::RIGHT_SIDEBAR_CLASS, 'control-sidebar-light') !!} ">
            {!! admin_section(AdminSection::RIGHT_SIDEBAR) !!}
        </aside>
    </div>

    {!! admin_section(\AdminSection::BODY_INNER_AFTER) !!}

    <!-- REQUIRED JS SCRIPTS -->
    {!! Dcat\Admin\Admin::js() !!}

</body>
</html>
