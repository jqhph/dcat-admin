@if($configData["main_layout_type"] == 'horizontal' && isset($configData["main_layout_type"]))
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbar_color'] }} navbar-fixed">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item">
                    <a href="{{ admin_url('/') }}" class="navbar-brand waves-effect waves-light">
                        <span class="logo-mini">{!! config('admin.logo-mini') !!}</span>
                        <span class="logo-lg">{!! config('admin.logo') !!}</span>
                    </a>
                </li>
            </ul>
        </div>
@else
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbar_class'] }} navbar-light navbar-shadow {{ $configData['navbar_color'] }}">
@endif
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-md-none mr-auto">
                            <a class="nav-link nav-menu-main menu-toggle " href="#">
                                <i class="ficon feather icon-menu"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="navbar-collapse">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        {!! Dcat\Admin\Admin::navbar()->render('left') !!}
                    </div>
                    <div class="float-right d-flex align-items-center">
                        {!! Dcat\Admin\Admin::navbar()->render() !!}
                    </div>
                    <ul class="nav navbar-nav float-right">
                        {{--User Account Menu--}}
                        {!! admin_section(AdminSection::NAVBAR_USER_PANEL) !!}

                        {!! admin_section(AdminSection::NAVBAR_AFTER_USER_PANEL) !!}
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    {{-- Search Start Here --}}
    <ul class="main-search-list-defaultlist d-none">

    </ul>
    <ul class="main-search-list-defaultlist-other-list d-none">
        <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer">
            <a class="d-flex align-items-center justify-content-between w-100 py-50">
                <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No
    results found.</span></div>
            </a>
        </li>
    </ul>
    {{-- Search Ends --}}