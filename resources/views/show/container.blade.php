<div class="row">
    <div class="col-md-12">{!! $panel !!}</div>

    @if($relations->count())
        <div class="col-md-12">
            <div class="row show-relation-container">
                @foreach($relations as $relation)
                    <div class="col-md-{{ $relation->width ?: 12 }}">
                        {!!  $relation->render() !!}
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>