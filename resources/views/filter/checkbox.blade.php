<div class="input-group input-group-sm">
    @php
        $checkbox = new \Dcat\Admin\Widgets\Checkbox($name.'[]', $options);
        if ($inline) $checkbox->inline();

        $checkbox->check(request($name, is_null($value) ? [] : $value))->circle(false);

    @endphp
    @if($showLabel)
        <div class="pull-left text-capitalize" style="margin-top: 6px;margin-right: 15px;">
            <b>{{ $label }}</b>
        </div>
        <div class="pull-left">
            {!! $checkbox !!}
        </div>
    @else
        {!! $checkbox !!}
    @endif
</div>
