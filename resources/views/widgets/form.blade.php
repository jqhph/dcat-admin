{!! $start !!}
    <div class="box-body fields-group p-0 pt-1">
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
                    @foreach($rows as $row)
                        {!! $row->render() !!}
                    @endforeach

                    @foreach($fields as $field)
                        @if($field instanceof \Dcat\Admin\Form\Field\Hidden)
                            {!! $field->render() !!}
                        @endif
                    @endforeach
                </div>
            @elseif($layout)
                {!! $layout->build() !!}
            @else
                @foreach($fields as $field)
                    {!! $field->render() !!}
                @endforeach
            @endif
        @endif
    </div>

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

@if(! empty($elementId))
<script>
    $('#{{ $elementId }}').form({
        validate: true,
        confirm: {!! json_encode($confirm) !!},
        success: function (data) {
            {!! $savedScript !!}
        },
        error: function (response) {
            {!! $errorScript !!}
        }
    });
</script>
@endif
