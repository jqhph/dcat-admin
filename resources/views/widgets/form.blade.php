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
            @include('admin::form.fields')
        @endif
    </div>

    {!! $footer !!}
{!! $end !!}
