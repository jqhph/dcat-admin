<span class="ext-name">
    {{ $value }}
</span>
@if($row->homepage)
    <a href='{!! $row->homepage !!}' target='_blank' class="feather icon-chrome"></a>
@endif

<div style="height: 10px"></div>

@if($row->version)

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
