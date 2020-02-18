<div class="input-group input-group-sm">
    @php
        $radio = new \Dcat\Admin\Widgets\Radio($name, $options);
        if ($inline) $radio->inline();

        $radio->checked(request($name, is_null($value) ? [] : $value));

    @endphp
    @if($showLabel)
        <div class="pull-left" style="margin-top: 6px;margin-right: 15px;">
            <b>{{ $label }}</b>
        </div>
        <div class="pull-left">
            {!! $radio !!}
        </div>
    @else
        {!! $radio !!}
    @endif
</div>