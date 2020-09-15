<div class="{{$viewClass['form-group']}}" >

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label pt-0">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}" id="{{ $id }}">

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
<script once>
    $('[name="_check_all_"]').on('change', function () {
        $(this).parents('.form-field').find('input[type="checkbox"]').prop('checked', this.checked);
    });
</script>
@endif