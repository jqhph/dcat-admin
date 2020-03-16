<body
        class="dcat-admin-body vertical-layout vertical-menu-modern 2-columns {{ $configData['blank_page_class'] }} {{ $configData['body_class']}} {{($configData['theme'] === 'light') ? '' : $configData['layout_theme'] }}  {{ $configData['vertical_menu_navbar_type'] }} {{ $configData['sidebar_class'] }} {{ $configData['footer_type'] }}"
        data-menu="vertical-menu-modern" data-col="2-columns" data-layout="{{ $configData['theme'] }}">

    <script>
        var Dcat = CreateDcat({!! Dcat\Admin\Admin::jsVariables() !!});

        console.log(123, Dcat)
        Dcat.ready(function () {

            setTimeout(function () {
            }, 1000)
        })
    </script>

    {!! admin_section(\AdminSection::BODY_INNER_BEFORE) !!}

    @include('admin::partials.sidebar')

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        @include('admin::partials.navbar')

        @if(($configData['content_layout']!=='default') && isset($configData['content_layout']))
            <div class="content-area-wrapper">
                <div class="{{ $configData['sidebar_position_class'] }}">
                    <div class="sidebar">
                        @yield('content-sidebar')
                    </div>
                </div>
                <div class="{{ $configData['content_sidebar_class'] }}">
                    <div class="content-wrapper" id="{{ $pjaxContainerId }}">
                        @yield('app')
                    </div>
                </div>
            </div>
        @else
            <div class="content-wrapper" id="{{ $pjaxContainerId }}">
                @yield('app')
            </div>
        @endif
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    @if($configData['main_layout_type'] == 'horizontal' && isset($configData['main_layout_type']))
    <footer
            class="footer {{ $configData['footer_type'] }} {{($configData['footer_type'] === 'footer-hidden') ? 'd-none':''}} footer-light navbar-shadow">
    @else
    <footer
            class="footer {{ $configData['footer_type'] }} {{($configData['footer_type']=== 'footer-hidden') ? 'd-none':''}} footer-light">
    @endif
        <p class="clearfix blue-grey lighten-2 mb-0">
            <span class="text-center d-block d-md-inline-block mt-25">
                Powered by
                <a target="_blank" href="https://github.com/jqhph/dcat-admin">Dcat Admin</a>
                <span>&nbsp;·&nbsp;</span>
                v{{ Dcat\Admin\Admin::VERSION }}
            </span>

            <button class="btn btn-primary btn-icon scroll-top" type="button">
                <i class="feather icon-arrow-up"></i>
            </button>
        </p>
    </footer>

    {!! admin_section(\AdminSection::BODY_INNER_AFTER) !!}

        <!-- REQUIRED JS SCRIPTS -->
    {!! Dcat\Admin\Admin::assets()->renderJs() !!}

</body>

</html>