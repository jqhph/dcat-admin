@if($showHeader)
<div class="box-header with-border">
    <h3 class="box-title" style="line-height:30px">{!! $form->title() !!}</h3>
    <div class="pull-right">{!! $form->renderTools() !!}</div>
</div>
@endif
<div class="box-body" style="padding:{{ $tabObj->isEmpty() ? '18px 0 12px' : '0'}}">
    @if(!$tabObj->isEmpty())
        @include('admin::form.tab', compact('tabObj', 'form'))
    @else
        <div class="fields-group">
            @if($form->hasRows())
                @foreach($form->getRows() as $row)
                    {!! $row->render() !!}
                @endforeach
            @else
                @foreach($form->fields() as $field)
                    {!! $field->render() !!}
                @endforeach
            @endif
        </div>
    @endif
</div>
{!! $form->renderFooter() !!}

@foreach($form->getHiddenFields() as $field)
    {!! $field->render() !!}
@endforeach
