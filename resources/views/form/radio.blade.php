<div class="{{$viewClass['form-group']}}" >

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <input type="hidden" name="{{$name}}">

        {!! $radio !!}

        @include('admin::form.help-block')

    </div>
</div>
