<div class="btn-group filter-button-group dropdown" style="margin-right:3px">
    <button
            class="btn btn-primary {{ $btn_class }}"
            @if($only_scopes)data-toggle="dropdown"@endif
            @if($scopes->isNotEmpty()) style="border-right: 0" @endif
    >
        <i class="feather icon-filter"></i>@if($filter_text)<span class="d-none d-sm-inline">&nbsp;&nbsp;{{ trans('admin.filter') }}</span>@endif

        @if($valueCount) &nbsp;({!! $valueCount !!}) @endif
    </button>
    @if($scopes->isNotEmpty())
        <ul class="dropdown-menu" role="menu">
            @foreach($scopes as $scope)
                {!! $scope->render() !!}
            @endforeach
            <li role="separator" class="dropdown-divider"></li>
            <li class="dropdown-item"><a href="{{ $url_no_scopes }}">{{ trans('admin.cancel') }}</a></li>
        </ul>
        <button type="button" class="btn btn-primary" data-toggle="dropdown" style="border-left: 0">
            @if($current_label) <span>{{ $current_label }}&nbsp;</span>@endif <i class="feather icon-chevron-down"></i>
        </button>
    @endif
</div>

