<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div id="{{$id}}" class="{{$class}}" {!! $attributes !!}>
            <textarea class="d-none" name="{{$name}}" placeholder="{{ $placeholder }}">{!! old($column, $value) !!}</textarea>
        </div>

        @include('admin::form.help-block')

    </div>
</div>
