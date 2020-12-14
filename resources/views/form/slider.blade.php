<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <input type="text" class="{{$class}}" name="{{$name}}" data-from="{{ $value }}" {!! $attributes !!} />

        @include('admin::form.help-block')

    </div>
</div>

<script require="@ionslider" init="{!! $selector !!}">
    setTimeout(function () {
        $this.ionRangeSlider({!! admin_javascript_json($options) !!})
    }, 400);
</script>