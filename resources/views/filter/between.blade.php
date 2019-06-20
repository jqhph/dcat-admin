<div class="filter-input col-sm-{{ $width }} "  style="{!! $style !!}">
    <div class="form-group" >
        <div class="input-group input-group-sm">
            <span class="input-group-addon"><b>{!! $label !!}</b></span>
            <input type="text" class="form-control" placeholder="{{$label}}" name="{{$name['start']}}" value="{{ request($name['start'], \Illuminate\Support\Arr::get($value, 'start')) }}">
            <span class="input-group-addon" style="border-left: 0; border-right: 0;">To</span>
            <input type="text" class="form-control" placeholder="{{$label}}" name="{{$name['end']}}" value="{{ request($name['end'], \Illuminate\Support\Arr::get($value, 'end')) }}">
        </div>
    </div>
</div>