@if($title || $tools)
<div class="box-header with-border">
    <h3 class="box-title" style="line-height:30px;">{!! $title !!}</h3>
    <div class="pull-right">{!! $tools !!}</div>
</div>
@endif
<div class="box-body">
    <div class="form-horizontal">
        @foreach($fields as $field)
            {!! $field->render() !!}
        @endforeach
        <div class="clearfix"></div>
    </div>
</div>
