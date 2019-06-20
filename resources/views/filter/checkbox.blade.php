<div class="input-group input-group-sm">
    @php
        $checkbox = new \Dcat\Admin\Widgets\Checkbox($name, $options);
        if ($inline) $checkbox->inline();

        $checkbox->checked(request($name, is_null($value) ? [] : $value));

    @endphp
    {!! $checkbox !!}
</div>