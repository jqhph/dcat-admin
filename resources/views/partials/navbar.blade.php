
<nav class="header-navbar navbar-expand-lg navbar
    navbar-with-menu {{ $configData['navbar_class'] }}
    {{ $configData['navbar_color'] }}
        navbar-light navbar-shadow " style="top: 0;">

    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav">
                    <li class="nav-item mr-auto">
                        <a class="nav-link nav-menu-main menu-toggle " data-widget="pushmenu" href="#">
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