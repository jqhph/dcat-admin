<div class="btn-group default" style="margin-right:3px">
    <button type="button" class="btn btn-sm btn-default dropdown-toggle btn-mini" data-toggle="dropdown">
        <span class="hidden-xs">{{ trans('admin.action') }}&nbsp;&nbsp;</span>
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @foreach($actions as $action)
            @if($action instanceof \Dcat\Admin\Grid\Tools\BatchDelete)
                {!! $action->render() !!}
            @else
                <li><a href="#" class="{{ $action->getElementClass(false) }}">{{ $action->getTitle() }}</a></li>
            @endif
        @endforeach
    </ul>
</div>