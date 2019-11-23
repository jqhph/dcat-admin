{!! $start !!}
<div class="box-body fields-group">

    @foreach($fields as $field)
        {!! $field->render() !!}
    @endforeach

</div>

{!! $end !!}
