@extends('admin::grid.displayer.editinline.template')

@section('field')
    <textarea class="form-control ie-input" rows="{{ $rows }}"></textarea>
@endsection

<script>
@section('popover-content')
    $template.find('textarea').text($trigger.data('value'));
@endsection

@section('popover-shown')
    $popover.find('.ie-input').focus();
@endsection
</script>


