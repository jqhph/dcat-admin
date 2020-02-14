<div class="btn-group default" style="margin-right:3px">
    <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-mini" data-toggle="dropdown">
        <span class="hidden-xs">{{ trans('admin.action') }}&nbsp;</span>
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @foreach($actions as $action)
            {!! $action->render() !!}
        @endforeach
    </ul>
</div>