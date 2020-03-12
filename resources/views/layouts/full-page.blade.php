<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-textdirection="{{ env('MIX_CONTENT_DIRECTION') === 'rtl' ? 'rtl' : 'ltr' }}">

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
</head>

<body
      class="dcat-admin-body vertical-layout vertical-menu-modern 1-column {{ $configData['blank_page_class'] }} {{ $configData['body_class'] }} {{($configData['theme'] === 'light') ? '' : $configData['theme'] }}"
        data-menu="vertical-menu-modern" data-col="1-column" data-layout="{{ $configData['theme'] }}">

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">

            {{-- Include Startkit Content --}}
            @yield('content')

        </div>
    </div>
</div>
<!-- End: Content-->

{!! admin_section(\AdminSection::BODY_INNER_AFTER) !!}

<!-- REQUIRED JS SCRIPTS -->
{!! Dcat\Admin\Admin::assets()->renderJs() !!}

</body>

</html>