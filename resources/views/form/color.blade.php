<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <div for="{{ $id }}" class="{{$viewClass['label']}} control-label">
        <span>{!! $label !!}</span>
    </div>

    <div class="{{$viewClass['field']}}" style="width: 210px;flex: none">

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