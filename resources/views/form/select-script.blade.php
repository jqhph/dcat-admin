
@include('admin::scripts.select')

<script require="@select2?lang={{ config('app.locale') === 'en' ? '' : str_replace('_', '-', config('app.locale')) }}" init="{!! $selector !!}">
    var configs = {!! admin_javascript_json($configs) !!};

    @yield('admin.select-ajax')

    @if(isset($remoteOptions))
    $.ajax({!! admin_javascript_json($remoteOptions) !!}).done(function(data) {
        configs.data = data;

        $this.each(function (_, select) {
            select = $(select);

            select.select2(configs);

            var value = select.data('value') + '';

            if (value) {
                select.val(value.split(',')).trigger("change")
            }
        });
    });
    @else
    $this.select2(configs);
    @endif

    {!! $cascadeScript !!}
</script>

@if(isset($loads))
    {{--loads联动--}}
    <script once>
        var selector = '{!! $selector !!}';

        var fields = '{!! $loads['fields'] !!}'.split('^');
        var urls = '{!! $loads['urls'] !!}'.split('^');

        $(document).off('change', selector);
        $(document).on('change', selector, function () {
            Dcat.helpers.loadFields(this, {
                group: '.fields-group',
                urls: urls,
                fields: fields,
                textField: "{{ $loads['textField'] }}",
                idField: "{{ $loads['idField'] }}",
            });
        });
        $(selector).trigger('change');
    </script>
@endif

@yield('admin.select-load')
