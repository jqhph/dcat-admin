<div class="d-flex">
    @if($row->logo)
        <img data-action='preview-img' src='{!! $row->logo !!}' style='max-width:40px;max-height:40px;cursor:pointer' class='img img-thumbnail' />&nbsp;&nbsp;
    @endif

    <span class="ext-name">
        @if($row->homepage)
            <a href='{!! $row->homepage !!}' target='_blank' class="feather {{ $linkIcon }}"></a>
        @endif

        @if($row->alias)
            {{ $row->alias }} <br><small class="text-80">{{ $value }}</small>
        @else
            {{ $value }}
        @endif
    </span>

    @if($row->new_version || ! $row->version)
        &nbsp;
        <span class="badge bg-primary">New</span>
    @endif
</div>

<div style="height: 10px"></div>

@if($row->type === Dcat\Admin\Extend\ServiceProvider::TYPE_THEME)
    <span>{{ trans('admin.theme') }}</span>
@endif

@if($row->version)
    @if($row->type === Dcat\Admin\Extend\ServiceProvider::TYPE_THEME)
        &nbsp;|&nbsp;
    @endif

    @if($row->enabled)
        {!! $disableAction !!}
    @else
        {!! $enableAction !!}
    @endif

    <span class="hover-display" onclick="$(this).css({display: 'inline'})">
        | {!! $uninstallAction !!}
    </span>

@endif

<style>
    .badge {
        max-height: 22px
    }
    .hover-display {
        display:none;
    }
    table tbody tr:hover .hover-display {
        display: inline;
    }
    .ext-name {
        font-size: 1.15rem;
    }
</style>
