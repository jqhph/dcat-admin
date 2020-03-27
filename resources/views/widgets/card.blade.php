<div {!! $attributes !!}>
    @if ($title || $tools)
        <div class="card-header {{ $divider ? 'with-border' : '' }}">
            <span class="card-box-title">{!! $title !!}</span>
            <div class="box-tools pull-right">
                @foreach($tools as $tool)
                    {!! $tool !!}
                @endforeach
            </div>
        </div>
    @endif
    <div class="card-body" style="{!! $padding !!}">
        {!! $content !!}
    </div>
    @if($footer)
    <div class="card-footer">
        {!! $footer !!}
    </div>
    @endif
</div>