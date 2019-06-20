<div class="{{$viewClass['form-group']}} {!! !$errors->has($column) ?: 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>
    <div class="{{$viewClass['field']}} select-resource">
        @include('admin::form.error')

        <app></app>

        <div class="input-group">
            @if(!$disabled)
                <input name="{{$name}}" type="hidden" />
            @endif
            <div {!! $attributes !!}>
            </div>
            <div class="input-group-btn">
                <div class="btn btn-{{$style}} " id="{{$formId}}{!! $class !!}-select-source-btn">
                    &nbsp;<i class="fa fa-long-arrow-up"></i>&nbsp;
                </div>
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>
<script data-exec-on-popstate>
    LA.ready(function () {
        LA.ResourceSelector({
            title: '{!! ucfirst(trans('admin.choose')) !!} {!! $label !!}', {{--弹窗标题--}}
            column: "{!! $name !!}", {{--字段名称--}}
            source: '{!! $source !!}', {{--资源地址--}}
            selector: '#{{$formId}}{!! $class !!}-select-source-btn', {{--选择按钮选择器--}}
            maxItem: {!! (int)$maxItem !!}, {{--最大选项数量，0为不限制--}}
            area: {!! $area !!},
            items: {!! $value !!}, {{--默认选中项，key => value 键值对--}}
            placeholder: '{!! $placeholder !!}',
            showCloseButton: false,
            closeButtonText: '{!! ucfirst(trans('admin.close')) !!}',
            exceedMaxItemTip: '{{trans('admin.selected_must_less_then', ['num' => $maxItem])}}',
            selectedOptionsTip: '{{trans('admin.selected_options')}}',
            disabled: '{!! $disabled !!}',
            displayer: 'navList',
            $displayerContainer: $('#{{$inputContainerId}}'),
        });
    });
</script>