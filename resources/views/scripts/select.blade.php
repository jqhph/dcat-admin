<script>
@section('admin.select-ajax')
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
@overwrite
</script>

@section('admin.select-load')
    @if(isset($load))
    <script once>
        var selector = '{!! $selector !!}';

        $(document).off('change', selector);
        $(document).on('change', selector, function () {
            var target = $(this).closest('{{ $load['group'] ?? '.fields-group' }}').find(".{{ $load['class'] }}");

            if (String(this.value) !== '0' && ! this.value) {
                return;
            }
            target.find("option").remove();

            $.ajax("{!! $load['url'].(strpos($load['url'],'?')?'&':'?') !!}q="+this.value).then(function (data) {
                $.map(data, function (d) {
                    target.append(new Option(d.{{ $load['textField'] }}, d.{{ $load['idField'] }}, false, false));
                });
                target.val(String(target.attr('data-value')).split(',')).trigger('change');
            });
        });
        $(selector).trigger('change');
    </script>
    @endif
@overwrite

@section('admin.select-lang')
@if (config('app.locale') !== 'en')
    {{--本地化--}}
    <script once>
        @php
            $lang = trans('select2') ?: [];
            $locale = str_replace('_', '-', config('app.locale'));
        @endphp
        if ($.fn.select2) {
            var e = $.fn.select2.amd;

            e.define("select2/i18n/{{ $locale }}", [], function () {
                return {
                    errorLoading: function () {
                        return "{{ $lang['error_loading'] ?? '' }}"
                    }, inputTooLong: function (e) {
                        return "{{ $lang['input_too_long'] ?? '' }}".replace(':num', e.input.length - e.maximum)
                    }, inputTooShort: function (e) {
                        return "{{ $lang['input_too_short'] ?? '' }}".replace(':num', e.minimum - e.input.length)
                    }, loadingMore: function () {
                        return "{{ $lang['loading_more'] ?? '' }}"
                    }, maximumSelected: function (e) {
                        return "{{ $lang['maximum_selected'] ?? '' }}".replace(':num', e.maximum)
                    }, noResults: function () {
                        return "{{ $lang['no_results'] ?? '' }}"
                    }, searching: function () {
                        return "{{ $lang['searching'] ?? '' }}"
                    }
                }
            }), {define: e.define, require: e.require}
        }
    </script>
@endif
@overwrite
