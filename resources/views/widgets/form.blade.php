{!! $start !!}
    <div class="box-body fields-group">

        @foreach($fields as $field)
            {!! $field->render() !!}
        @endforeach

    </div>

    @if ($method != 'GET')
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @endif
    
    <!-- /.box-body -->
    @if($buttons['submit'] || $buttons['reset'])
    <div class="box-footer row" style="display: flex">
        <div class="col-md-{{$width['label']}}"> &nbsp;</div>

        <div class="col-md-{{ $width['field'] }}">
            @if(! empty($buttons['reset']))
                <button type="reset" class="btn btn-white pull-left"><i class="feather icon-rotate-ccw"></i> {{ trans('admin.reset') }}</button>
            @endif

            @if(! empty($buttons['submit']))
                <button type="submit" class="btn btn-primary pull-right"><i class="feather icon-save"></i> {{ trans('admin.submit') }}</button>
            @endif
        </div>
    </div>
    @endif
{!! $end !!}
