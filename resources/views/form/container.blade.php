@if($showHeader)
    <div class="box-header with-border mb-1" style="padding: .65rem 1rem">
        <h3 class="box-title" style="line-height:30px">{!! $form->title() !!}</h3>
        <div class="pull-right">{!! $form->renderTools() !!}</div>
    </div>
@endif
<div class="box-body" {!! $tabObj->isEmpty() && !$form->hasRows() ? 'style="margin-top: 6px"' : '' !!} >
    @if(!$tabObj->isEmpty())
        @include('admin::form.tab', compact('tabObj', 'form'))
    @else
        <div class="fields-group">
            @include('admin::form.fields', ['rows' => $form->rows(), 'fields' => $form->fields(), 'layout' => $form->layout()])
        </div>
    @endif
</div>
{!! $form->renderFooter() !!}

{!! $form->renderHiddenFields() !!}
