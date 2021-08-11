@extends('admin::grid.displayer.editinline.template')

@section('field')
    <input class="form-control ie-input"/>
@endsection

<script>
@section('popover-content')
    $template.find('input').attr('value', $trigger.data('value'));
@endsection

@section('popover-shown')
    @if(! empty($mask))
    $popover.find('.ie-input').inputmask({!! admin_javascript_json($mask) !!});
    @endif
@endsection
</script>
