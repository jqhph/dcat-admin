<style>
    .editormd-fullscreen {z-index: 99999999;}
</style>

<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="{{$class}}" {!! $attributes !!}>
            <textarea class="d-none" name="{{$name}}" placeholder="{{ $placeholder }}">{!! $value !!}</textarea>
        </div>

        @include('admin::form.help-block')

    </div>
</div>

<script require="@markdown" init="{!! $selector !!}">
    editormd(id, {!! $options !!});
</script>
