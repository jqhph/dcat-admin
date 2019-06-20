<div {!! $attributes !!}>
    <div class="dropdown btn-group {!! $options['show_tool_shadow'] ? '' : 'no-shadow' !!} pull-right">
        @foreach($options['tools'] as $tool)
            {!! $tool !!}
        @endforeach
    </div>

    <h4 class="header-title m-t-0 m-b-25">{!! $options['title'] !!}</h4>

    <div>
        <div>
            <div class="right-content pull-right">{!! $options['content']['right'] !!}</div>

            <h2 class="main-content m-b-10" >
                {!! $options['content']['left'] !!}&nbsp;
            </h2>
            <p class="text-muted">
                {!! $options['description'] !!}&nbsp;
            </p>
        </div>

        @if($options['progress'])
        <div class="progress progress-sm m-b-0">
            <div data-width="{!! $options['progress']['percent'] !!}%" class="progress-bar progress-bar-{!! $options['progress']['style'] !!}" >
                <span class="sr-only"></span>
            </div>
        </div>
        @endif

    </div>
</div>