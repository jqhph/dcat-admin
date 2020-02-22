<div class="hidden">
    <div style="{!! $style !!}"  id="{{ $filterID }}">
        <form action="{!! $action !!}" class="form-horizontal" pjax-container method="get">
            <div class="right-side-filter-container">
                <div class="pull-left">
                    <button type="submit" class=" btn btn-sm btn-primary submit">
                        <i class="fa fa-search"></i> &nbsp;{{ __('admin.search') }}
                    </button>&nbsp;
                    @if(!$disableResetButton)
                        <a href="{!! $action !!}" class="reset btn btn-sm btn-default">
                            <i class="fa fa-undo"></i> &nbsp;{{ __('admin.reset') }}
                        </a>
                    @endif
                </div>

                <div class="pull-right">
                    <span class="btn btn-trans close-slider font-16">
                        <i class="ti-shift-right"></i>
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
