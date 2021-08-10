@extends('admin::grid.displayer.editinline.template')

@section('field')
    <input class="form-control ie-input"/>
@endsection

<script>
@section('popover-content')
    $template.find('input').attr('value', $trigger.data('value'));
@endsection

@section('popover-shown')
    $popover.find('.ie-input').focus();
    @if(! empty($mask))
    $popover.find('.ie-input').inputmask(@json($mask));
    @endif
@endsection
</script>
