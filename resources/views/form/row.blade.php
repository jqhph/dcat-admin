<div class="row" style="margin-bottom: 8px">
    @foreach($fields as $field)
    <div class="col-md-{{ $field['width'] }}">
        {!! $field['element']->render() !!}
    </div>
    @endforeach
</div>