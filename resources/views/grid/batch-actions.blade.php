@if(! $isHoldSelectAllCheckbox)
<div class="btn-group dropdown  {{$selectAllName}}-btn" style="display:none;margin-right: 3px;z-index: 100">
    <button type="button" class="btn btn-white dropdown-toggle btn-mini" data-toggle="dropdown">
        <span class="d-none d-sm-inline selected"></span>
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @foreach($actions as $action)
            <li class="dropdown-item">
                {!! $action->render() !!}
            </li>
        @endforeach
    </ul>
</div>
@endif