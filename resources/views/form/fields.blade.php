@if($rows)
    <div class="ml-2 mb-2 mr-2" style="margin-top: -0.5rem">
        @foreach($rows as $row)
            {!! $row->render() !!}
        @endforeach

        @foreach($fields as $field)
            @if($field instanceof Dcat\Admin\Form\Field\Hidden)
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