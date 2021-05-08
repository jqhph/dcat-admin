@if($grid->isAsyncRequest())
    {!! $grid->renderHeader() !!}

    <div class="table-responsive table-wrapper {{ $grid->option('table_collapse') ? 'table-collapse' : '' }}">
        <div class="tables-container">
            <div class="table-wrap table-main" data-height="{{ $tableHeight }}">
                <table class="custom-data-table async-table {{ $grid->formatTableClass() }}" id="{{ $tableId }}">
                    <thead>
                    @if ($headers = $grid->getVisibleComplexHeaders())
                        <tr>
                            @foreach($headers as $header)
                                {!! $header->render() !!}
                            @endforeach
                        </tr>
                    @endif
                    <tr>
                        @foreach($grid->getVisibleColumns() as $column)
                            <th {!! $column->formatTitleAttributes() !!}>{!! $column->getLabel() !!}{!! $column->renderHeader() !!}</th>
                        @endforeach
                    </tr>
                    </thead>

                    @if ($grid->hasQuickCreate())
                        {!! $grid->renderQuickCreate() !!}
                    @endif

                    <tbody>
                    @foreach($grid->rows() as $row)
                        <tr {!! $row->rowAttributes() !!}>
                            @foreach($grid->getVisibleColumnNames() as $name)
                                <td {!! $row->columnAttributes($name) !!}>
                                    {!! $row->column($name) !!}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    @if ($grid->rows()->isEmpty())
                        <tr>
                            <td colspan="{!! count($grid->getVisibleColumnNames()) !!}">
                                <div style="margin:5px 0 0 10px;"><span class="help-block" style="margin-bottom:0"><i class="feather icon-alert-circle"></i>&nbsp;{{ trans('admin.no_data') }}</span></div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>

            @if ($grid->leftVisibleColumns()->isNotEmpty() || $grid->leftVisibleComplexColumns()->isNotEmpty())
                <div class="table-wrap table-fixed table-fixed-left" data-height="{{ $tableHeight }}">
                    <table class="custom-data-table  {{ $grid->formatTableClass() }} ">
                        <thead>

                        @if ($grid->getVisibleComplexHeaders())
                            <tr>
                                @foreach($grid->leftVisibleComplexColumns() as $header)
                                    {!! $header->render() !!}
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($grid->leftVisibleComplexColumns() as $header)
                                    @if ($header->getColumnNames()->count() > 1)
                                        @foreach($header->columns() as $column)
                                            <th {!! $column->formatTitleAttributes() !!}>{!! $column->getLabel() !!}{!! $column->renderHeader() !!}</th>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                @foreach($grid->leftVisibleColumns() as $column)
                                    <th {!! $column->formatTitleAttributes() !!}>{!! $column->getLabel() !!}{!! $column->renderHeader() !!}</th>
                                @endforeach
                            </tr>
                        @endif
                        </thead>
                        <tbody>

                        @foreach($grid->rows() as $row)
                            <tr {!! $row->rowAttributes() !!}>
                                @foreach($grid->leftVisibleColumns() as $column)
                                    <td {!! $row->columnAttributes($column->getName()) !!}>
                                        {!! $row->column($column->getName()) !!}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($grid->rightVisibleColumns()->isNotEmpty() || $grid->rightVisibleComplexColumns()->isNotEmpty())
                <div class="table-wrap table-fixed table-fixed-right" data-height="{{ $tableHeight }}">
                    <table class="custom-data-table  {{ $grid->formatTableClass() }} ">
                        <thead>
                        @if ($grid->getVisibleComplexHeaders())
                            <tr>
                                @foreach($grid->rightVisibleComplexColumns() as $header)
                                    {!! $header->render() !!}
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($grid->rightVisibleComplexColumns() as $header)
                                    @if ($header->getColumnNames()->count() > 1)
                                        @foreach($header->columns() as $column)
                                            <th {!! $column->formatTitleAttributes() !!}>{!! $column->getLabel() !!}{!! $column->renderHeader() !!}</th>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                @foreach($grid->rightVisibleColumns() as $column)
                                    <th {!! $column->formatTitleAttributes() !!}>{!! $column->getLabel() !!}{!! $column->renderHeader() !!}</th>
                                @endforeach
                            </tr>
                        @endif

                        </thead>

                        <tbody>

                        @foreach($grid->rows() as $row)
                            <tr {!! $row->rowAttributes() !!}>
                                @foreach($grid->rightVisibleColumns() as $column)
                                    <td {!! $row->columnAttributes($column->getName()) !!}>
                                        {!! $row->column($column->getName()) !!}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {!! $grid->renderFooter() !!}

    {!! $grid->renderPagination() !!}
@else
<div class="dcat-box custom-data-table async-{{ $tableId }}">
    @include('admin::grid.table-toolbar')

    {!! $grid->renderFilter() !!}

    <div class="async-body">
        {!! $grid->renderHeader() !!}

        <div class="table-responsive table-wrapper {{ $grid->option('table_collapse') ? 'table-collapse' : '' }}">
            <div class="tables-container">
                <div class="table-wrap table-main" data-height="{{ $tableHeight }}">
                    <table class="custom-data-table async-table {{ $grid->formatTableClass() }}" id="{{ $tableId }}">
                        <thead>
                        @if ($headers = $grid->getVisibleComplexHeaders())
                            <tr>
                                @foreach($headers as $header)
                                    {!! $header->render() !!}
                                @endforeach
                            </tr>
                        @endif
                        <tr>
                            @foreach($grid->getVisibleColumns() as $column)
                                <th {!! $column->formatTitleAttributes() !!}>{!! $column->getLabel() !!}{!! $column->renderHeader() !!}</th>
                            @endforeach
                        </tr>
                        </thead>

                        @if ($grid->hasQuickCreate())
                            {!! $grid->renderQuickCreate() !!}
                        @endif

                        <tbody>
                            <tr>
                                <td colspan="{!! count($grid->getVisibleColumnNames()) !!}">
                                    <div style="margin:5px 0 0 10px;"><span class="help-block" style="margin-bottom:0"><i class="feather icon-alert-circle"></i>&nbsp;{{ trans('admin.no_data') }}</span></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {!! $grid->renderFooter() !!}
    </div>
</div>
@endif
