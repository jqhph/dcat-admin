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


@if(isset($loads))
{{--loads联动--}}
<script once>
    var selector = '{!! $selector !!}';

    var fields = '{!! $loads['fields'] !!}'.split('^');
    var urls = '{!! $loads['urls'] !!}'.split('^');

    $(document).off('change', selector);
    $(document).on('change', selector, function () {
        Dcat.helpers.loadFields(this, {
            group: '{{ $loads['group'] ?? '.fields-group' }}',
            urls: urls,
            fields: fields,
            textField: "{{ $loads['textField'] }}",
            idField: "{{ $loads['idField'] }}",
        });
    });
    $(selector).trigger('change');
</script>
@endif

<script once>
    // on first focus (bubbles up to document), open the menu
    $(document).off('focus', '.select2-selection.select2-selection--single')
        .on('focus', '.select2-selection.select2-selection--single', function (e) {
            $(this).closest(".select2-container").siblings('select:enabled').select2('open');
        });

    // steal focus during close - only capture once and stop propogation
    $(document).off('select2:closing', 'select.select2')
        .on('select2:closing', 'select.select2', function (e) {
            $(e.target).data("select2").$selection.one('focus focusin', function (e) {
                e.stopPropagation();
            });
        });
</script>
