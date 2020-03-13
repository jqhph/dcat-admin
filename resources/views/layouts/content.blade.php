@section('content-header')
    <section class="content-header breadcrumbs-top mb-1">
        @if($header || $description)
            <h1 class=" float-left">
                {!! $header !!}
                <small>{!! $description !!}</small>
            </h1>
        @elseif($breadcrumb || config('admin.enable_default_breadcrumb'))
            <div>&nbsp;</div>
        @endif

        @include('admin::partials.breadcrumb')

    </section>
@endsection

@section('content')
    @include('admin::partials.alerts')
    @include('admin::partials.exception')

    {!! $content !!}

    @include('admin::partials.toastr')
@endsection

@section('app')
    {!! Dcat\Admin\Admin::assets()->renderStyle() !!}

    {{-- 页面埋点--}}
    {!! admin_section(AdminSection::APP_INNER_BEFORE) !!}
    <div class="content-header">
        @yield('content-header')
    </div>

    <div class="content-body" id="app">
        @yield('content')
    </div>

    {{-- 页面埋点--}}
    {!! admin_section(AdminSection::APP_INNER_AFTER) !!}

    {!! Dcat\Admin\Admin::assets()->renderScript() !!}
    {!! Dcat\Admin\Admin::html() !!}
@endsection

@if(! request()->pjax())
    @include('admin::layouts.page')
@else
    <title>{{ Dcat\Admin\Admin::title() }} @if($header) | {{ $header }}@endif</title>

    <script>Dcat.pjaxResponded()</script>

    {!! Dcat\Admin\Admin::assets()->renderCss() !!}
    {!! Dcat\Admin\Admin::assets()->renderJs() !!}

    @yield('app')
@endif