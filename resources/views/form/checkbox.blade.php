<div class="{{$viewClass['form-group']}} {!! !$errors->has($column) ?: 'has-error' !!}" >

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}" id="{{ $id }}">

        @include('admin::form.error')

        {!! $checkbox !!}

        <input type="hidden" name="{{$name}}[]">

        @include('admin::form.help-block')

    </div>
</div>
