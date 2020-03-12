<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-textdirection="{{ $configData['direction'] === 'rtl' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@if(! empty($header)){{ $header }} | @endif {{ Dcat\Admin\Admin::title() }}</title>

    @if(! config('admin.disable_no_referrer_meta'))
        <meta name="referrer" content="no-referrer"/>
    @endif

    @if(! empty($favicon = Dcat\Admin\Admin::favicon()))
        <link rel="shortcut icon" href="{{$favicon}}">
    @endif

    {!! admin_section(\AdminSection::HEAD) !!}

    {!! Dcat\Admin\Admin::assets()->renderCss() !!}

    {!! Dcat\Admin\Admin::assets()->renderHeaderJs() !!}

    @yield('head')
</head>

<body
      class="dcat-admin-body vertical-layout vertical-menu-modern 1-column {{ $configData['blank_page_class'] }} {{ $configData['body_class'] }} {{($configData['theme'] === 'light') ? '' : $configData['theme'] }}"
        data-menu="vertical-menu-modern" data-col="1-column" data-layout="{{ $configData['theme'] }}">

@include('admin::partials.script')

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            @yield('content')
        </div>
    </div>
</div>

{!! admin_section(\AdminSection::BODY_INNER_AFTER) !!}

{!! Dcat\Admin\Admin::assets()->renderJs() !!}

</body>
</html>