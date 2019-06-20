<div class="{{$viewClass['form-group']}} {!! !$errors->has($column) ?: 'has-error' !!}" {!! $inline ? 'style="margin-bottom:23px"' : '' !!}>

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}" id="{{$id}}">

        @include('admin::form.error')

        @php
            $checkbox = new \Dcat\Admin\Widgets\Checkbox($name.'[]', $options, $checkboxStyle);

            if($inline) $checkbox->inline();
            if ($disabled) $checkbox->disabled();
            // {{ ($option == old($column, $value)) || ($value === null && in_array($label, $checked)) ?'checked':'' }}
            $checkbox->checked(old($column, $value));
            $checkbox->circle($circle);

        @endphp
        {!! $checkbox !!}

        <input type="hidden" name="{{$name}}[]">

        @include('admin::form.help-block')

    </div>
</div>
