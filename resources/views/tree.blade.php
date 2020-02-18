<div class="card-header">

    <div class="btn-group" style="margin-right:3px">
        <a class="btn btn-primary btn-sm {{ $id }}-tree-tools" data-action="expand">
            <i class="fa fa-plus-square-o"></i>&nbsp;<span class="hidden-xs">{{ trans('admin.expand') }}</span>
        </a>
        <a class="btn btn-primary btn-sm {{ $id }}-tree-tools" data-action="collapse">
            <i class="fa fa-minus-square-o"></i><span class="hidden-xs">&nbsp;{{ trans('admin.collapse') }}</span>
        </a>
    </div>

    @if($useSave)
    <div class="btn-group" style="margin-right:3px">
        <a class="btn btn-primary btn-sm {{ $id }}-save" ><i class="fa fa-save"></i><span class="hidden-xs">&nbsp;{{ trans('admin.save') }}</span></a>
    </div>
    @endif

    @if($useRefresh)
    <div class="btn-group" style="margin-right:3px">
        <a class="btn btn-custom btn-sm" data-action="refresh" ><i class="fa fa-refresh"></i><span class="hidden-xs">&nbsp;{{ trans('admin.refresh') }}</span></a>
    </div>
    @endif

    @if($tools)
    <div class="btn-group" style="margin-right:3px">
        {!! $tools !!}
    </div>
    @endif

    {!! $createButton !!}

</div>

<div class="card-body table-responsive">
    <div class="dd" id="{{ $id }}" style="margin:18px">
        <ol class="dd-list">
            @if($items)
                @foreach($items as $branch)
                    @include($branchView)
                @endforeach
            @else
                <span class="help-block" style="margin-bottom:0"><i class="fa fa-info-circle"></i>&nbsp;{{ trans('admin.no_data') }}</span>
            @endif
        </ol>
    </div>
</div>
