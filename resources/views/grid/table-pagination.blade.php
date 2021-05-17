@if ($grid->allowPagination())
    <div class="box-footer d-block clearfix ">
        {!! $grid->paginator()->render() !!}
    </div>
@endif
