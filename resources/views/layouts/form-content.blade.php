<script>Dcat.pjaxResponded();</script>

<style>
    .form-content .row {
        margin-right: 0;
        margin-left: 0;
    }
</style>

{{--必须在静态资源加载前，用section先渲染 content--}}
@section('content')
    <section class="form-content">{!! $content !!}</section>
@endsection

{!! Dcat\Admin\Admin::asset()->cssToHtml() !!}
{!! Dcat\Admin\Admin::asset()->jsToHtml() !!}

{!! Dcat\Admin\Admin::asset()->styleToHtml() !!}

@yield('content')

{!! Dcat\Admin\Admin::asset()->scriptToHtml() !!}
<div class="extra-html">{!! Dcat\Admin\Admin::html() !!}</div>

{{--select2下拉选框z-index必须大于弹窗的值--}}
<style>.select2-dropdown {z-index: 99999999999}</style>
