<div {!! $attributes !!}>
    <div class="box-header with-border">
        <h3 class="box-title">{!! $title !!}</h3>
        <div class="box-tools pull-right">
            @foreach($tools as $tool)
                {!! $tool !!}
            @endforeach
        </div>
    </div>
    <div class="box-body collapse show" style="{!! $padding !!}">
        {!! $content !!}
    </div>
</div>