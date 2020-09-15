<script require="@select2">
    var configs = {!! json_encode($configs) !!};

    @if(isset($ajax))
        configs = $.extend(configs, {
        ajax: {
            url: "{{ $ajax['url'] }}",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: $.map(data.data, function (d) {
                        d.id = d.{{ $ajax['idField'] }};
                        d.text = d.{{ $ajax['textField'] }};
                        return d;
                    }),
                    pagination: {
                        more: data.next_page_url
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
    @endif

    @if(isset($remoteOptions))
    $.ajax({!! json_encode($remoteOptions) !!}).done(function(data) {
        configs.data = data;

        $("{!! $selector !!}").each(function (_, select) {
            select = $(select);

            select.select2(configs);

            var value = select.data('value') + '';

            if (value) {
                select.val(value.split(',')).trigger("change")
            }
        });
    });
    @else
    $("{!! $selector !!}").select2(configs);
    @endif
</script>

@if(isset($load))
    <script once>
        var selector = '{!! $selector !!}';

        $(document).off('change', selector);
        $(document).on('change', selector, function () {
            var target = $(this).closest('.fields-group').find(".{{ $load['class'] }}");

            if (String(this.value) !== '0' && ! this.value) {
                return;
            }
            $.ajax("{{ $load['url'] }}?q="+this.value).then(function (data) {
                target.find("option").remove();
                $(target).select2({
                    data: $.map(data, function (d) {
                        d.id = d.{{ $load['idField'] }};
                        d.text = d.{{ $load['textField'] }};
                        return d;
                    })
                }).val(target.attr('data-value').split(',')).trigger('change');
            });
        });
        $(selector).trigger('change');
    </script>
@endif

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
                promises.push(refreshOptions(urls[index] + "?q="+ _this.value, target));
            });

            $.when(promises).then(function() {});
        });
        $(selector).trigger('change');
    </script>
@endif

{{--本地化--}}
<script once>
    @php
        $lang = trans('select2');
        $locale = config('app.locale');
    @endphp
    if ($.fn.select2) {
        var e = $.fn.select2.amd;

        e.define("select2/i18n/{{ $locale }}", [], function () {
            return {
                errorLoading: function () {
                    return "{{ $lang['error_loading'] }}"
                }, inputTooLong: function (e) {
                    return "{{ $lang['input_too_long'] }}".replace(':num', e.input.length - e.maximum)
                }, inputTooShort: function (e) {
                    return "{{ $lang['input_too_short'] }}".replace(':num', e.minimum - e.input.length)
                }, loadingMore: function () {
                    return "{{ $lang['loading_more'] }}"
                }, maximumSelected: function (e) {
                    return "{{ $lang['maximum_selected'] }}".replace(':num', e.maximum)
                }, noResults: function () {
                    return "{{ $lang['no_results'] }}"
                }, searching: function () {
                    return "{{ $lang['searching'] }}"
                }
            }
        }), {define: e.define, require: e.require}
    }
</script>
