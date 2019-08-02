@section('content')
    <section class="content">
        @include('admin::partials.alerts')
        @include('admin::partials.exception')

        {!! $content !!}
    </section>
@endsection

@section('app')
    {!! Dcat\Admin\Admin::style() !!}

    {!! admin_section(AdminSection::APP_INNER_BEFORE) !!}

    <div id="app" style="padding:5px">
        @yield('content')
    </div>

    {!! admin_section(AdminSection::APP_INNER_AFTER) !!}

    {!! Dcat\Admin\Admin::script() !!}
    {!! Dcat\Admin\Admin::html() !!}
@endsection


@if(!request()->pjax())
    @include('admin::contents.simple-index', ['header' => $header])
@else
    <title>{{ Dcat\Admin\Admin::title() }} @if($header) | {{ $header }}@endif</title>

    <script>LA.pjaxresponse = true;</script>

    {!! Dcat\Admin\Admin::css() !!}
    {!! Dcat\Admin\Admin::js() !!}

    @yield('app')
@endif