<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')
        <input name="{{$name}}" type="hidden" value="0" />
        <input type="checkbox" name="{{$name}}" class="{{$class}} la_checkbox" {{ old($column, $value) == 1 ? 'checked' : '' }} {!! $attributes !!} />

        @include('admin::form.help-block')

    </div>
</div>
