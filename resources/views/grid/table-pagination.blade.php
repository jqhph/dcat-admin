@if ($paginator = $grid->paginator())
    <div class="box-footer d-block clearfix " style="border:0;padding: 1rem 1.5rem 1.3rem;">
        {!! $paginator->render() !!}
    </div>
@else
    <div class="box-footer d-block clearfix text-80 " style="border:0;padding: 1rem 1.5rem 1.3rem;height:48px;line-height:25px;">
        @if ($grid->rows()->isEmpty())
            {!! trans('admin.pagination.range', ['first' => '<b>0</b>', 'last' => '<b>'.$grid->rows()->count().'</b>', 'total' => '<b>'.$grid->rows()->count().'</b>',]) !!}
        @else
            {!! trans('admin.pagination.range', ['first' => '<b>1</b>', 'last' => '<b>'.$grid->rows()->count().'</b>', 'total' => '<b>'.$grid->rows()->count().'</b>',]) !!}
        @endif
    </div>
@endif