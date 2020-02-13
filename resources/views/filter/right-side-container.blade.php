<div class="hidden">
    <div style="{!! $style !!}"  id="{{ $filterID }}">
        <form action="{!! $action !!}" class="form-horizontal" pjax-container method="get">
            <div class="right-side-filter-container">
                <div class="pull-left">
                    <button type="submit" class=" btn btn-trans submit">
                        <i class="fa fa-search"></i> &nbsp;{{ __('admin.search') }}
                    </button>
                    @if(!$disableResetButton)
                        <a href="{!! $action !!}" class="reset btn btn-trans btn-default">
                            <i class="fa fa-undo"></i> &nbsp;{{ __('admin.reset') }}
                        </a>
                    @endif
                </div>

                <div class="pull-right">
                    <span class="btn btn-trans close-slider">
                        <i class=" ti-shift-right"></i>
                    </span>
                </div>
            </div>

            @foreach($layout->columns() as $column)
                @foreach($column->filters() as $filter)
                    {!! $filter->render() !!}
                @endforeach
            @endforeach
        </form>
    </div>
</div><?php
