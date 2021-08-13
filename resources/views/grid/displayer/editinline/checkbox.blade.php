@extends('admin::grid.displayer.editinline.template')

@section('field')
    {!! $checkbox !!}
@endsection

<script>
@section('popover-content')
    $template.find('input[type=checkbox]').each(function (index, checkbox) {
        if($.inArray($(checkbox).attr('value'), $trigger.data('value')) >= 0) {
            $(checkbox).attr('checked', true);
        }
    });
@endsection
</script>
