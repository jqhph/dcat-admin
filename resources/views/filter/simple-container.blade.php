<div class="filter-box card p-2 {{ $expand ? '' : 'd-none' }} {{$containerClass}}" style="padding-bottom: .5rem!important;margin-top: 10px;margin-bottom: 8px;box-shadow: 0 2px 3px 0 rgba(0, 0, 0, 0.04);">
    <div class="card-body" style="{!! $style !!}"  id="{{ $filterID }}">
        <form action="{!! $action !!}" class="form-horizontal grid-filter-form" pjax-container method="get">
            <div class="row mt-1 mb-0">
                @foreach($layout->columns() as $column)
                    @foreach($column->filters() as $filter)
                        {!! $filter->render() !!}
                    @endforeach
                @endforeach

                <div class="btn-group ml-1 mb-1" style="height: fit-content;margin-right: 10px">
                    <button class="btn btn-primary btn-sm btn-mini submit">
                        <i class="feather icon-search"></i><span class="d-none d-sm-inline">&nbsp;&nbsp;{{ trans('admin.search') }}</span>
                    </button>
                </div>
                <div class="btn-group btn-group-sm default btn-mini" style="height: fit-content"  >
                    @if(!$disableResetButton)
                        <a  href="{!! $action !!}" class="reset btn btn-white btn-sm ">
                            <i class="feather icon-rotate-ccw"></i><span class="d-none d-sm-inline">&nbsp;&nbsp;{{ trans('admin.reset') }}</span>
                        </a>
                    @endif
                </div>
            </div>

        </form>
    </div>
</div>