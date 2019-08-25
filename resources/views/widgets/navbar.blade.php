<nav {!! $attributes !!}>
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#{{$id}}">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">{!! $title !!}</a>
        </div>

        <div class="navbar-collapse collapse" id="{{$id}}">
            @if($items['left'])
            <ul class="nav navbar-nav">
                @foreach($items['left'] as $k => $item)
                    @if($k === '__dropdown__')
                        <li class="dropdown">
                            {!! $item !!}
                        </li>
                    @else
                        {!! $item !!}
                    @endif
                @endforeach
            </ul>
            @endif

            @if($items['right'])
                <ul class="nav navbar-nav navbar-right">
                    @foreach($items['right'] as $k => $item)
                        @if($k === '__dropdown__')
                            <li class="dropdown">
                                {!! $item !!}
                            </li>
                        @else
                            {!! $item !!}
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</nav>