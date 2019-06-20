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
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>.content-wrapper,.sidebar-mini.sidebar-collapse .content-wrapper{margin-left:0!important}</style>
</head>

<body class="dcat-admin-body hold-transition">
    {!! admin_section(\AdminSection::BODY_INNER_BEFORE) !!}

    @include('admin::partials.script')

    <div class="wrapper">
        <div class="content-wrapper" id="pjax-container" style="min-height:1500px">
            @yield('app')
        </div>
    </div>

    {!! admin_section(\AdminSection::BODY_INNER_AFTER) !!}

    <!-- REQUIRED JS SCRIPTS -->
    {!! Dcat\Admin\Admin::js() !!}

</body>
</html>
