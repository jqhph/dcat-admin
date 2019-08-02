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

    @if(Session::has('layer-msg'))
        @php
            $bag     = Session::get('layer-msg');
            $type    = $bag->get('type')[0] ?? 'success';
            $message = $bag->get('message')[0] ?? '';
            $offset  = $bag->get('offset')[0] ?? '';
        @endphp
        <script>$(function () { LA.{{$type}}('{!!  $message  !!}', '{{ $offset }}'); })</script>
    @endif
@endsection


@section('app')
    {!! Dcat\Admin\Admin::style() !!}

    {!! admin_section(AdminSection::APP_INNER_BEFORE) !!}

    <div id="app">
        @yield('content')
    </div>

    {!! admin_section(AdminSection::APP_INNER_AFTER) !!}

    {!! Dcat\Admin\Admin::script() !!}
    {!! Dcat\Admin\Admin::html() !!}
@endsection


@if(!request()->pjax())
    @include('admin::index', ['header' => $header])
@else
    <title>{{ Dcat\Admin\Admin::title() }} @if($header) | {{ $header }}@endif</title>

    <script>LA.pjaxresponse = true;</script>

    {!! Dcat\Admin\Admin::css() !!}
    {!! Dcat\Admin\Admin::js() !!}

    @yield('app')
@endif