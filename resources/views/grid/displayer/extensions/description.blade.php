<div style="margin-bottom: 10px">{{ $value }}</div>

@if($row->version && empty($row->new_version))
    {{ trans('admin.version').' '.$row->version }}

    @if($settingAction)
        &nbsp;|&nbsp;
        {!! $settingAction !!}
    @endif
@else
    {!! $updateAction !!}

    @if($settingAction && $row->new_version)
        &nbsp;|&nbsp;
        {!! $settingAction !!}
    @endif
@endif
&nbsp;|&nbsp;

<a href="javascript:void(0)">{{ trans('admin.view') }}</a>