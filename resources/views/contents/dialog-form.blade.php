{{--标记为pjax加载，这样能保证LA.ready内的逻辑在js脚本加载完毕后执行--}}
<script>LA.pjaxresponse = 1;</script>

{{--必须在静态资源加载前，用section先渲染 content--}}
@section('content')
    <section class="content">{!! $content !!}</section>
@endsection

{!! Dcat\Admin\Admin::css() !!}
{!! Dcat\Admin\Admin::js() !!}

{!! Dcat\Admin\Admin::style() !!}

@yield('content')

{!! Dcat\Admin\Admin::script() !!}
{!! Dcat\Admin\Admin::html() !!}

{{--select2下拉选框z-index必须大于弹窗的值--}}
<style>.select2-dropdown {z-index: 99999999999}</style>
