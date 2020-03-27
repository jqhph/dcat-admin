<div class="btn-group filter-button-group btn-no-shadow dropdown" style="margin-right:3px">
    <button
            class="btn btn-outline-primary {{ $btn_class }}"
            @if($only_scopes)data-toggle="dropdown"@endif
            @if($scopes->isNotEmpty()) style="border-right: 0" @endif
    >
        <i class="feather icon-filter"></i>@if($show_filter_text)<span class="d-none d-sm-inline">&nbsp;&nbsp;{{ trans('admin.filter') }}</span>@endif
    </button>
    @if($scopes->isNotEmpty())
        <ul class="dropdown-menu" role="menu">
            @foreach($scopes as $scope)
                {!! $scope->render() !!}
            @endforeach
            <li role="separator" class="dropdown-divider"></li>
            <li class="dropdown-item"><a href="{{ $url_no_scopes }}">{{ trans('admin.cancel') }}</a></li>
        </ul>
        <button type="button" class="btn btn-outline-primary" data-toggle="dropdown" style="padding: 0.75rem 1rem !important;border-left: 0">
            @if($current_label) <span>{{ $current_label }}&nbsp;</span>@endif <i class="feather icon-chevron-down"></i>
        </button>
    @endif
</div>

