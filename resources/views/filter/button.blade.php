<div class="btn-group filter-button-group btn-no-shadow" style="margin-right:3px">
    <label class="btn btn-outline-primary {{ $scopes->isNotEmpty() ? 'dropdown-toggle' : '' }} {{ $btn_class }}" @if($only_scopes)data-toggle="dropdown"@endif>
        <i class="feather icon-filter"></i>@if($show_filter_text)<span class="hidden-xs">&nbsp;&nbsp;{{ trans('admin.filter') }}</span>@endif
    </label>
    @if($scopes->isNotEmpty())
        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">

            <span>{{ $current_label }}</span>
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            @foreach($scopes as $scope)
                {!! $scope->render() !!}
            @endforeach
            <li role="separator" class="divider"></li>
            <li><a href="{{ $url_no_scopes }}">{{ trans('admin.cancel') }}</a></li>
        </ul>
    @endif
</div>

