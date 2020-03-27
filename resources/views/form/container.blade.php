@if($showHeader)
    <div class="box-header with-border mb-1">
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
                @foreach($form->rows() as $row)
                    {!! $row->render() !!}
                @endforeach
            @else
                @foreach($form->fields() as $field)
                    @if(! $field->hasAttribute('block-field'))
                    {!! $field->render() !!}
                    @endif
                @endforeach
            @endif
        </div>
    @endif
</div>
{!! $form->renderFooter() !!}

@foreach($form->hiddenFields() as $field)
    {!! $field->render() !!}
@endforeach
