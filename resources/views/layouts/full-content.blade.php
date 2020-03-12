@section('content')
    <section class="content">
        @include('admin::partials.alerts')
        @include('admin::partials.exception')

        {!! $content !!}
    </section>
@endsection

@section('app')
    {!! Dcat\Admin\Admin::assets()->renderStyle() !!}

    {!! admin_section(AdminSection::APP_INNER_BEFORE) !!}

    <div class="content-body" id="app">
        @yield('content')
    </div>

    {!! admin_section(AdminSection::APP_INNER_AFTER) !!}

    {!! Dcat\Admin\Admin::assets()->renderScript() !!}
    {!! Dcat\Admin\Admin::html() !!}
@endsection


@if(!request()->pjax())
    @include('admin::layouts.full-page', ['header' => $header])
@else
    <title>{{ Dcat\Admin\Admin::title() }} @if($header) | {{ $header }}@endif</title>

    <script>Dcat.pjaxResponded();</script>

    {!! Dcat\Admin\Admin::assets()->renderCss() !!}
    {!! Dcat\Admin\Admin::assets()->renderJs() !!}

    @yield('app')
@endif