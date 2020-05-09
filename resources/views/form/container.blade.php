@if($showHeader)
    <div class="box-header with-border mb-1" style="padding: .65rem 1rem">
        <h3 class="box-title" style="line-height:30px">{!! $form->title() !!}</h3>
        <div class="pull-right">{!! $form->renderTools() !!}</div>
    </div>
@endif
<div class="box-body">
    @if(!$tabObj->isEmpty())
        @include('admin::form.tab', compact('tabObj', 'form'))
    @else
        <div class="fields-group">
            @if($form->hasRows())
                <div class="ml-2 mb-2">
                    @foreach($form->rows() as $row)
                        {!! $row->render() !!}
                    @endforeach
                </div>
            @else
                @foreach($form->fields() as $field)
                    {!! $field->render() !!}
                @endforeach
            @endif
        </div>
    @endif
</div>
{!! $form->renderFooter() !!}

@foreach($form->hiddenFields() as $field)
    {!! $field->render() !!}
@endforeach
