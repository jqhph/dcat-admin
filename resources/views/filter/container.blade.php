<style>
    .filter-box {
        border-top: 1px solid #eee;
        margin-top: 10px;
        margin-bottom: -.5rem!important;
        padding: 1.8rem;
    }
</style>

<div class="filter-box shadow-0 card mb-0 {{ $expand ? '' : 'd-none' }} {{$containerClass}}">
    <div class="card-body" style="{!! $style !!}"  id="{{ $filterID }}">
        <form action="{!! $action !!}" class="form-horizontal" pjax-container method="get">
            <div class="row mb-0">
                @foreach($layout->columns() as $column)
                    @foreach($column->filters() as $filter)
                        {!! $filter->render() !!}
                    @endforeach
                @endforeach

                <button class="btn btn-primary btn-sm btn-mini submit" style="margin-left: 12px">
                    <i class="feather icon-search"></i><span class="d-none d-sm-inline">&nbsp;&nbsp;{{ trans('admin.search') }}</span>
                </button>

                @if(!$disableResetButton)
                <a style="margin-left: 6px" href="{!! $action !!}" class="reset btn btn-white btn-sm ">
                    <i class="feather icon-rotate-ccw"></i><span class="d-none d-sm-inline">&nbsp;&nbsp;{{ trans('admin.reset') }}</span>
                </a>
                @endif
            </div>

        </form>
    </div>
</div>
