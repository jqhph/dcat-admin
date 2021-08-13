@extends('admin::grid.displayer.editinline.template')

@section('field')
    {!! $radio !!}
@endsection

<script>
@section('popover-content')
    $template.find('input[type=radio]').each(function (index, checkbox) {
        if(String($(checkbox).attr('value')) === String($trigger.data('value'))) {
            $(checkbox).attr('checked', true);
        }
    });
@endsection
</script>
