<div class="show-field form-group row">
    <div class="col-sm-2 control-label">
        <span>{{ $label }}</span>
    </div>

    <div class="col-sm-{{ $width }}">
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