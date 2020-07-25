<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <div for="{{ $id }}" class="{{$viewClass['label']}} control-label">
        <span>{!! $label !!}</span>
    </div>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group" style="flex-wrap: nowrap">

            @if ($prepend)
                <span class="input-group-prepend"><span class="input-group-text bg-white">{!! $prepend !!}</span></span>
            @endif
            <input {!! $attributes !!} />

            @if ($append)
                <span class="input-group-append">{!! $append !!}</span>
            @endif
        </div>

        @include('admin::form.help-block')

    </div>
</div>