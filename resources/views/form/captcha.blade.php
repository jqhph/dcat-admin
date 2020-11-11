<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group" style="width: 250px;">

            <input {!! $attributes !!} />

            <span class="input-group-addon clearfix" style="padding: 1px;">
                <img class="field-refresh-captcha" data-url="{{ $captchaSrc }}" src="{{ $captchaSrc }}" style="height:30px;cursor: pointer;"  title="Click to refresh"/>
            </span>
        </div>

        @include('admin::form.help-block')

    </div>
</div>

<script init=".field-refresh-captcha" once>
    $this.off('click').on('click', function () {
        $(this).attr('src', $(this).attr('data-url')+'?'+Math.random());
    });
</script>