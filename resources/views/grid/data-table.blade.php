
<div class="dcat-box custom-data-table dt-bootstrap4">

    @include('admin::grid.table-toolbar')

    {!! $grid->renderFilter() !!}

    {!! $grid->renderHeader() !!}

    <div class="table-responsive table-wrapper">
            <table class="custom-data-table dataTable {{ $grid->formatTableClass() }}" id="{{ $tableId }}">
                <thead>
                @if ($headers = $grid->getComplexHeaders())
                    <tr>
                        @foreach($headers as $header)
                            {!! $header->render() !!}
                        @endforeach
                    </tr>
                @endif
                <tr>
                    @foreach($grid->columns() as $column)
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
                        @foreach($grid->getColumnNames() as $name)
                            <td {!! $row->columnAttributes($name) !!}>
                                {!! $row->column($name) !!}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                @if ($grid->rows()->isEmpty())
                    <tr>
                        <td colspan="{!! count($grid->getColumnNames()) !!}">
                            <div style="margin:5px 0 0 10px;"><span class="help-block" style="margin-bottom:0"><i class="feather icon-alert-circle"></i>&nbsp;{{ trans('admin.no_data') }}</span></div>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

    {!! $grid->renderFooter() !!}

    @include('admin::grid.table-pagination')

</div>
