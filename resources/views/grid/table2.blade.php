
<div class="dcat-box data-list-view dt-bootstrap4">

    @if ($grid->allowToolbar())
        <div class="data-list-view-header">
            <div class="table-responsive">
                <div class="top" style="padding: 0">
                @if(!empty($title))
                    <h4 style="margin:5px 10px 0;">
                        {!! $title !!}&nbsp;
                        @if(!empty($description))
                            <small>{!! $description!!}</small>
                        @endif
                    </h4>
                    <div data-responsive-table-toolbar="{{$tableId}}">
                        {!! $grid->renderTools() !!} {!! $grid->renderCreateButton() !!} {!! $grid->renderExportButton() !!}
                    </div>
                @else
                    <div>
                        {!! $grid->renderTools() !!}
                    </div>

                    <div data-responsive-table-toolbar="{{$tableId}}">
                        {!! $grid->renderCreateButton() !!} {!! $grid->renderExportButton() !!}
                    </div>
                @endif
                </div>
            </div>
        </div>
    @endif

    {!! $grid->renderFilter() !!}

    {!! $grid->renderHeader() !!}

    <div class="in table-responsive" style="{!! $grid->option('show_bordered') ? 'padding:3px 10px 10px' : '' !!}">
        <table class="table data-list-view dataTable dt-checkboxes-select {{ $grid->option('show_bordered') ? 'table-bordered' : $grid->option('table_header_style') }} " id="{{ $tableId }}">
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
                        <div style="margin:5px 0 0 10px;"><span class="help-block" style="margin-bottom:0"><i class="fa fa-info-circle"></i>&nbsp;{{ trans('admin.no_data') }}</span></div>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    {!! $grid->renderFooter() !!}

    @if ($paginator = $grid->paginator())
        <div class="box-footer clearfix " style="padding-bottom:5px;">
            {!! $paginator->render() !!}
        </div>
    @else
        <div class="box-footer clearfix text-80 " style="height:48px;line-height:25px;">
            @if ($grid->rows()->isEmpty())
                {!! trans('admin.pagination.range', ['first' => '<b>0</b>', 'last' => '<b>'.$grid->rows()->count().'</b>', 'total' => '<b>'.$grid->rows()->count().'</b>',]) !!}
            @else
                {!! trans('admin.pagination.range', ['first' => '<b>1</b>', 'last' => '<b>'.$grid->rows()->count().'</b>', 'total' => '<b>'.$grid->rows()->count().'</b>',]) !!}
            @endif
        </div>
    @endif

</div>
