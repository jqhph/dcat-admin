<div class="show-field form-group row">
    <div class="col-sm-{{ $width['label'] }} control-label">
        <span>{{ $label }}</span>
    </div>

    <div class="col-sm-{{ $width['field'] }}">
        @if($wrapped)
            <div class="box box-solid box-default no-margin box-show">
                <div class="box-body">
                    @if($escape)
                        {{ $content }}
                    @else
                        {!! $content !!}
                    @endif
                    &nbsp;
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
</div>