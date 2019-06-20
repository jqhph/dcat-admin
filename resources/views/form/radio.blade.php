<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}" {!! $inline ? 'style="margin-bottom:23px"' : '' !!}>

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        @php
            $radio = new \Dcat\Admin\Widgets\Radio($name, $options, $radioStyle);

            if($inline) $radio->inline();
            if ($disabled) $radio->disabled();
            // {{ ($option == old($column, $value)) || ($value === null && in_array($label, $checked)) ?'checked':'' }}
            $radio->checked(old($column, $value));

        @endphp
        {!! $radio !!}

        @include('admin::form.help-block')

    </div>
</div>
