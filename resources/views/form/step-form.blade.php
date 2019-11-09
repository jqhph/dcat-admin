{!! $start !!}
<div class="box-body fields-group">

    @foreach($fields as $field)
        {!! $field->render() !!}
    @endforeach

</div>

<!-- /.box-body -->
@if(count($buttons) > 0)
    <div class="box-footer">
        <div class="col-md-2"></div>

        <div class="col-md-8">
            @if(in_array('previous', $buttons))
                <div class="btn-group pull-left">
                    <span class="btn btn-warning pull-right previous-step">{!! trans('admin.') !!}</span>
                </div>
            @endif

            @if(in_array('next', $buttons))
                <div class="btn-group pull-right">
                    <span class="btn btn-primary pull-right next-step">{!! trans('admin.next') !!}</span>
                </div>
            @endif
        </div>
    </div>
@endif
{!! $end !!}
