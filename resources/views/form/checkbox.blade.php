<div class="{{$viewClass['form-group']}}" >

    <label class="{{$viewClass['label']}} control-label pt-0">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @if($checkAll)
            {!! $checkAll !!}
            <hr style="margin-top: 10px;margin-bottom: 0;">
        @endif

        @include('admin::form.error')

        {!! $checkbox !!}

        <input type="hidden" name="{{$name}}[]">

        @include('admin::form.help-block')

    </div>
</div>

@if(! empty($canCheckAll))
<script init="[name='_check_all_']" once>
    $this.on('change', function () {
        $(this).parents('.form-field').find('input[type="checkbox"]:not(:first)').prop('checked', this.checked).trigger('change');
    });
</script>
@endif