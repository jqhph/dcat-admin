<div class="grid-dropdown-actions dropdown">
    <a href="#" style="padding:0 10px;" class="dropdown-toggle " data-toggle="dropdown">
        <i class="fa fa-ellipsis-v"></i>
    </a>
    <ul class="dropdown-menu" style="min-width:120px !important;left: -65px;">

        @foreach($default as $action)
            <li>{!! \Dcat\Admin\Support\Helper::render($action) !!}</li>
        @endforeach

        @if(!empty($custom))

            @if(!empty($default))
                <li class="divider"></li>
            @endif

            @foreach($custom as $action)
                <li>{!! $action !!}</li>
            @endforeach
        @endif
    </ul>
</div>