<div class="{{$viewClass['form-group']}}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <input type="text" class="{{$class}}" name="{{$name}}" data-from="{{ $value }}" {!! $attributes !!} />

        @include('admin::form.help-block')

    </div>
</div>

<script require="@ionslider">
    setTimeout(function () {
        $('{{ $selector }}').ionRangeSlider({!! $options !!})
    }, 400);
</script>