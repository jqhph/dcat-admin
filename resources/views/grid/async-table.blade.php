@if($grid->isAsyncRequest())
    {!! $grid->renderHeader() !!}

    <div class="{!! $grid->formatTableParentClass() !!}">
        <table class="{{ $grid->formatTableClass() }}" id="{{ $tableId }}" >
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

    {!! $grid->renderFooter() !!}

    {!! $grid->renderPagination() !!}

@else
    <div class="dcat-box async-{{ $tableId }}">

        <div class="d-block pb-0">
            @include('admin::grid.table-toolbar')
        </div>

        {!! $grid->renderFilter() !!}

        <div class="async-body">
            <div class="{!! $grid->formatTableParentClass() !!}">
                <table class="{{ $grid->formatTableClass() }}" id="{{ $tableId }}" >
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

                    <tbody>
                    <tr>
                        <td colspan="{!! count($grid->getVisibleColumnNames()) !!}">
                            &nbsp;
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <script>
    Dcat.ready(function () {
        var reqName = '{!! Dcat\Admin\Grid::ASYNC_NAME !!}',
            $box = $('.async-{{ $tableId }}'),
            $body = $box.find('.async-body'),
            url = '{!! $asyncUrl !!}',
            loading = false;

        function render(url) {
            if (loading || url.indexOf('javascript:') !== -1) {
                return;
            }
            loading = true;

            $body.find('table').loading({style:'height:250px', background:'transparent'});

            if (url.indexOf('?') === -1) {
                url += '?';
            }

            if (url.indexOf(reqName) === -1) {
                url += '&'+reqName+'=1'
            }

            history.pushState({}, '', url.replace(reqName+'=1', ''));

            $box.data('current', url);

            Dcat.helpers.asyncRender(url, function (html) {
                loading = false;

                $body.html(html);

                $box.find('.grid-refresh').off('click').on('click', function () {
                    render($box.data('current'));

                    return false;
                });

                $box.find('.pagination .page-link').on('click', loadLink);
                $box.find('.per-pages-selector .dropdown-item a').on('click', loadLink);
                $box.find('.grid-column-header a').on('click', loadLink);

                $box.find('form').off('submit').on('submit', function () {
                    var action = $(this).attr('action');
                    if (action.indexOf('?') === -1) {
                        action += '?';
                    }

                    render(action+'&'+$(this).serialize());

                    return false;
                });

                $box.find('.filter-box .reset').on('click', loadLink);

                $box.find('.grid-selector a').on('click', loadLink);
            });
        }

        function loadLink() {
            render($(this).attr('href'));

            return false;
        }

        // $table.on('grid:render', render);

        render(url);
    });
    </script>
@endif
