{!! $start !!}
    <div class="box-body fields-group pl-0 pr-0 pt-1" style="padding: 0 0 .5rem">
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
            @elseif($layout->hasColumns())
                {!! $layout->build() !!}
            @else
                @foreach($fields as $field)
                    {!! $field->render() !!}
                @endforeach
            @endif
        @endif
    </div>

    {!! $footer !!}
{!! $end !!}

@if(! empty($ajax))
<script>
    $('#{{ $elementId }}').form({
        validate: true,
        confirm: {!! admin_javascript_json($confirm) !!},
        success: function (data) {
            {!! $savedScript !!}
        },
        error: function (response) {
            {!! $errorScript !!}
        }
    });
</script>
@endif
