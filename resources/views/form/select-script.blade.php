
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

        var refreshOptions = function(url, target) {
            Dcat.loading();

            $.ajax(url).then(function(data) {
                Dcat.loading(false);
                target.find("option").remove();
                $(target).select2({
                    data: $.map(data, function (d) {
                        d.id = d.{{ $loads['idField'] }};
                        d.text = d.{{ $loads['textField'] }};
                        return d;
                    })
                }).val(String(target.data('value')).split(',')).trigger('change');
            });
        };

        $(document).off('change', selector);
        $(document).on('change', selector, function () {
            var _this = this;
            var promises = [];
            var values = [];

            $(this).find('option:selected').each(function () {
                if (String(this.value) === '0'|| this.value) {
                    values.push(this.value)
                }
            });

            fields.forEach(function(field, index){
                var target = $(_this).closest('.fields-group').find('.' + fields[index]);

                if (! values.length) {
                    return;
                }
                promises.push(refreshOptions(urls[index] + (urls[index].match(/\?/)?'&':'?') + "q="+ values.join(','), target));
            });

            $.when(promises).then(function() {});
        });
        $(selector).trigger('change');
    </script>
@endif

@yield('admin.select-load')
