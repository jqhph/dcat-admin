@section('content')
    <section class="content-header">
        @if($header || $description)
            <h1 style="display:inline">
                {!! $header !!}
                <small>{!! $description !!}</small>
            </h1>
        @elseif($breadcrumb || config('admin.enable_default_breadcrumb'))
            <div>&nbsp;</div>
        @endif

        @include('admin::partials.breadcrumb')

    </section>

    <section class="content">

        @include('admin::partials.alerts')
        @include('admin::partials.exception')

        {!! $content !!}

    </section>

    @if(Session::has('____'))
        @php
            $bag     = Session::get('layer-msg');
            $type    = $bag->get('type')[0] ?? 'success';
            $message = $bag->get('message')[0] ?? '';
            $offset  = $bag->get('offset')[0] ?? '';
        @endphp
        <script>$(function () { Dcat.{{$type}}('{!!  $message  !!}', '{{ $offset }}'); })</script>
    @endif
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

@if(! request()->pjax())
    @include('admin::layouts.page')
@else
    <title>{{ Dcat\Admin\Admin::title() }} @if($header) | {{ $header }}@endif</title>

    <script>Dcat.pjaxResponded()</script>

    {!! Dcat\Admin\Admin::assets()->renderCss() !!}
    {!! Dcat\Admin\Admin::assets()->renderJs() !!}

    @yield('app')
@endif