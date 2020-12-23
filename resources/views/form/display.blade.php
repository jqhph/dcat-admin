<div class="{{$viewClass['form-group']}}" style="margin-top: .3rem">
    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>
    <div class="{{$viewClass['field']}}">
        <div class="box box-solid box-default no-margin">
            <div class="box-body">
                {!! $value !!}&nbsp;
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>