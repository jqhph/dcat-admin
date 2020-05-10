@if($title || $tools)
<div class="box-header with-border" style="padding: .65rem 1rem">
    <h3 class="box-title" style="line-height:30px;">{!! $title !!}</h3>
    <div class="pull-right">{!! $tools !!}</div>
</div>
@endif
<div class="box-body">
    <div class="form-horizontal mt-1">
        @if($rows)
            <div>
                @foreach($rows as $row)
                    {!! $row->render() !!}
                @endforeach
            </div>
        @else
            @foreach($fields as $field)
                {!! $field->render() !!}
            @endforeach
        @endif
        <div class="clearfix"></div>
    </div>
</div>
