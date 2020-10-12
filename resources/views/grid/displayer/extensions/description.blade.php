<div style="margin-bottom: 10px">{{ $value }}</div>

@if($row->version)
    {{ trans('admin.version').' '.$row->version }}
@else
    {!! $updateAction !!}
@endif
 &nbsp;|&nbsp;

@if($settingAction)
    {!! $settingAction !!}
    &nbsp;|&nbsp;
@endif

<a href="javascript:void(0)">{{ trans('admin.view') }}</a>