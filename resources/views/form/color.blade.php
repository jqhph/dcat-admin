<style>
    .popover{z-index:29891015}
</style>

<div class="{{$viewClass['form-group']}}">
    <div  class="{{$viewClass['label']}} control-label">
        <span>{!! $label !!}</span>
    </div>

    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')

        <div class="input-group">

            <span class="input-group-prepend"><span class="input-group-text bg-white" style="padding: 4px"><i style="width: 24px;height: 100%;background: {!! $value !!}"></i></span></span>

            <input {!! $attributes !!} />

            @if ($append)
                <span class="input-group-append">{!! $append !!}</span>
            @endif
        </div>

        @include('admin::form.help-block')
    </div>
</div>

<script require="@color" init="{!! $selector !!}">
    $this.colorpicker({!! admin_javascript_json($options) !!}).on('colorpickerChange', function(event) {
        $(this).parents('.input-group').find('.input-group-prepend i').css('background-color', event.color.toString());
    });
</script>