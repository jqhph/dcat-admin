
@include('admin::scripts.select')

<script require="@select2" init="{!! $selector !!}">
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
            $.ajax(url).then(function(data) {
                target.find("option").remove();
                $(target).select2({
                    data: $.map(data, function (d) {
                        d.id = d.{{ $loads['idField'] }};
                        d.text = d.{{ $loads['textField'] }};
                        return d;
                    })
                }).val(target.data('value').split(',')).trigger('change');
            });
        };

        $(document).off('change', selector);
        $(document).on('change', selector, function () {
            var _this = this;
            var promises = [];

            fields.forEach(function(field, index){
                var target = $(_this).closest('.fields-group').find('.' + fields[index]);

                if (_this.value !== '0' && ! _this.value) {
                    return;
                }
                promises.push(refreshOptions(urls[index] + (urls[index].match(/\?/)?'&':'?') + "q="+ _this.value, target));
            });

            $.when(promises).then(function() {});
        });
        $(selector).trigger('change');
    </script>
@endif

@yield('admin.select-load')

{{--本地化--}}
@yield('admin.select-lang')