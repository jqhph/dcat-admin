<div class="line col-sm-{{ $width }} show-field">
    <div class="text">{{ $label }}</div>
    @if($wrapped)
        <div class="box box-solid box-default no-margin box-show">
            <div class="box-body">
                @if($escape)
                    {{ $content }}
                @else
                    {!! $content !!}
                @endif
            </div>
        </div>
    @else
        @if($escape)
            {{ $content }}
        @else
            {!! $content !!}
        @endif
    @endif
</div>