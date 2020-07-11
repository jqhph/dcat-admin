{!! $start !!}
    <div class="box-body fields-group p-0">
        @if(! $tabObj->isEmpty())
            @include('admin::form.tab', compact('tabObj'))

            @foreach($fields as $field)
                @if($field instanceof \Dcat\Admin\Form\Field\Hidden)
                    {!! $field->render() !!}
                @endif
            @endforeach
        @else
            @if($rows)
                <div class="ml-2 mb-2">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    @foreach($rows as $row)
                        {!! $row->render() !!}
                    @endforeach

                    @foreach($fields as $field)
                        @if($field instanceof \Dcat\Admin\Form\Field\Hidden)
                            {!! $field->render() !!}
                        @endif
                    @endforeach
                </div>
            @else
                @foreach($fields as $field)
                    {!! $field->render() !!}
                @endforeach
            @endif
        @endif
    </div>

    @if ($method != 'GET')
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @endif
    
    <!-- /.box-body -->
    @if($buttons['submit'] || $buttons['reset'])
    <div class="box-footer row" style="display: flex">
        <div class="col-md-2"> &nbsp;</div>

        <div class="col-md-8">
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
