<div class="card-header">

    <div class="btn-group" style="margin-right:3px">
        <button class="btn btn-primary btn-sm {{ $id }}-tree-tools" data-action="expand">
            <i class="feather icon-plus-square"></i>&nbsp;<span class="hidden-xs">{{ trans('admin.expand') }}</span>
        </button>
        <button class="btn btn-primary btn-sm {{ $id }}-tree-tools" data-action="collapse">
            <i class="feather icon-minus-square"></i><span class="hidden-xs">&nbsp;{{ trans('admin.collapse') }}</span>
        </button>
    </div>

    @if($useSave)
    &nbsp;<div class="btn-group" style="margin-right:3px">
        <button class="btn btn-primary btn-sm {{ $id }}-save" ><i class="feather icon-save"></i><span class="hidden-xs">&nbsp;{{ trans('admin.save') }}</span></button>
    </div>
    @endif

    @if($useRefresh)
        &nbsp;<div class="btn-group" style="margin-right:3px">
        <button class="btn btn-outline-custom btn-sm" data-action="refresh" ><i class="feather icon-refresh-cw"></i><span class="hidden-xs">&nbsp;{{ trans('admin.refresh') }}</span></button>
    </div>
    @endif

    @if($tools)
    &nbsp;<div class="btn-group" style="margin-right:3px">
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
