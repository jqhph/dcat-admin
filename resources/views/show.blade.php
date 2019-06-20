<div class="row">
    <div class="col-md-12">{!! $panel !!}</div>

    @if($relations->count())
    <div class="col-md-12 show-relation-container" style="top:10px">
        @foreach($relations as $relation)
            {!!  $relation->render() !!}
        @endforeach
    </div>
    @endif
</div>