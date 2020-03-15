@if(! $isHoldSelectAllCheckbox)
<div class="btn-group dropdown  {{$selectAllName}}-btn" style="display:none;margin-right: 3px;">
    <button type="button" class="btn btn-white dropdown-toggle btn-mini" data-toggle="dropdown">
        <span class="hidden-xs selected"></span>
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @foreach($actions as $action)
            {!! $action->render() !!}
        @endforeach
    </ul>
</div>
@endif