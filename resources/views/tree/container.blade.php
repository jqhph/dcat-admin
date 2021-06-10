<div class="card-header pb-1 with-border" style="padding:.9rem 1rem">

    <div>
        <div class="btn-group" style="margin-right:3px">
            <button class="btn btn-primary btn-sm {{ $id }}-tree-tools" data-action="expand">
                <i class="feather icon-plus-square"></i>&nbsp;<span class="d-none d-sm-inline">{{ trans('admin.expand') }}</span>
            </button>
            <button class="btn btn-primary btn-sm {{ $id }}-tree-tools" data-action="collapse">
                <i class="feather icon-minus-square"></i><span class="d-none d-sm-inline">&nbsp;{{ trans('admin.collapse') }}</span>
            </button>
        </div>

        @if($useSave)
            &nbsp;<div class="btn-group" style="margin-right:3px">
                <button class="btn btn-primary btn-sm {{ $id }}-save" ><i class="feather icon-save"></i><span class="d-none d-sm-inline">&nbsp;{{ trans('admin.save') }}</span></button>
            </div>
        @endif

        @if($useRefresh)
            &nbsp;<div class="btn-group" style="margin-right:3px">
                <button class="btn btn-outline-primary btn-sm" data-action="refresh" ><i class="feather icon-refresh-cw"></i><span class="d-none d-sm-inline">&nbsp;{{ trans('admin.refresh') }}</span></button>
            </div>
        @endif

        @if($tools)
            &nbsp;<div class="btn-group" style="margin-right:3px">
                {!! $tools !!}
            </div>
        @endif
    </div>

    <div>
        {!! $createButton !!}
    </div>

</div>

<div class="card-body table-responsive">
    <div class="dd" id="{{ $id }}">
        <ol class="dd-list">
            @if($items)
                @foreach($items as $branch)
                    @include($branchView)
                @endforeach
            @else
                <span class="help-block" style="margin-bottom:0"><i class="feather icon-alert-circle"></i>&nbsp;{{ trans('admin.no_data') }}</span>
            @endif
        </ol>
    </div>
</div>

<script require="@jquery.nestable">
    var id = '{{ $id }}';
    var tree = $('#'+id);

    tree.nestable({!! admin_javascript_json($nestableOptions) !!});

    $('.'+id+'-save').on('click', function () {
        var serialize = tree.nestable('serialize'), _this = $(this);
        _this.buttonLoading();
        $.post({
            url: '{{ $url }}',
            data: {
                '{{ \Dcat\Admin\Tree::SAVE_ORDER_NAME }}': JSON.stringify(serialize)
            },
            success: function (data) {
                _this.buttonLoading(false);

                Dcat.handleJsonResponse(data)
            }
        });
    });

    $('.'+id+'-tree-tools').on('click', function(e){
        var action = $(this).data('action');
        if (action === 'expand') {
            tree.nestable('expandAll');
        }
        if (action === 'collapse') {
            tree.nestable('collapseAll');
        }
    });

    @if(! $expand)
    tree.nestable('collapseAll')
    @endif
</script>
