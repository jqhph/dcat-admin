<div class=" panel-collapse collapse {{ $expand?'in':'' }} {{$containerClass}}" style="{{$border}}padding:0;">
    <div style="{!! $style !!}"  id="{{ $filterID }}">
        <form action="{!! $action !!}" class="form-horizontal" pjax-container method="get">
            @foreach($layout->columns() as $column)
                @foreach($column->filters() as $filter)
                    {!! $filter->render() !!}
                @endforeach
            @endforeach
            <div class="pull-left" style="margin-bottom:10px">
                <div class="btn-group btn-group-sm" style="margin-left:5px;">
                    <button class="btn btn-primary btn-sm btn-mini submit">
                        <i class="fa fa-search"></i><span class="hidden-xs">&nbsp;&nbsp;{{ trans('admin.search') }}</span>
                    </button>
                </div>
                <div class="btn-group btn-group-sm default btn-mini" style="margin-left:5px"  >
                    @if(!$disableResetButton)
                    <a  href="{!! $action !!}" class="reset btn btn-default btn-sm ">
                        <i class="fa fa-undo"></i><span class="hidden-xs">&nbsp;&nbsp;{{ trans('admin.reset') }}</span>
                    </a>
                    @endif

                </div>
            </div>
            <div style="clear:both"></div>
        </form>
    </div>
</div>