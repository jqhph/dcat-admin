<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}" >

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}" id="{{ $id }}">

        @include('admin::form.error')

        <input type="hidden" name="{{$name}}">

        {!! $radio !!}

        @include('admin::form.help-block')

    </div>
</div>
