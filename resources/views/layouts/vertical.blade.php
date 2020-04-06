<body
        class="dcat-admin-body sidebar-mini {{ $configData['blank_page_class'] }} {{ $configData['body_class']}} {{ $configData['sidebar_class'] }}" >

    <script>
        var Dcat = CreateDcat({!! Dcat\Admin\Admin::jsVariables() !!});
    </script>

    {!! admin_section(\AdminSection::BODY_INNER_BEFORE) !!}

    @include('admin::partials.sidebar')

    <div class="app-content content">
        <div class="content-overlay"></div>
{{--        <div class="header-navbar-shadow"></div>--}}

        @include('admin::partials.navbar')

        <div class="content-wrapper" id="{{ $pjaxContainerId }}">
            @yield('app')
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <footer class="footer {{ $configData['footer_type'] }} {{($configData['footer_type']=== 'footer-hidden') ? 'd-none':''}} footer-light">
        <p class="clearfix blue-grey lighten-2 mb-0">
            <span class="text-center d-block d-md-inline-block mt-25">
                Powered by
                <a target="_blank" href="https://github.com/jqhph/dcat-admin">Dcat Admin</a>
                <span>&nbsp;·&nbsp;</span>
                v{{ Dcat\Admin\Admin::VERSION }}
            </span>

            <button class="btn btn-primary btn-icon scroll-top" type="button" style="bottom: 2%">
                <i class="feather icon-arrow-up"></i>
            </button>
        </p>
    </footer>

    {!! admin_section(\AdminSection::BODY_INNER_AFTER) !!}

    {!! Dcat\Admin\Admin::asset()->jsToHtml() !!}

    <script>Dcat.boot();</script>

</body>

</html>