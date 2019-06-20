<div class="input-group input-group-sm">
    @php
        $radio = new \Dcat\Admin\Widgets\Radio($name, $options);
        if ($inline) $radio->inline();

        $radio->checked(request($name, is_null($value) ? [] : $value));

    @endphp
    {!! $radio !!}
</div>