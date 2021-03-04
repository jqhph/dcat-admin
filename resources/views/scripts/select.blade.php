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
            Dcat.helpers.loadField(this, {
                group: '{{ $load['group'] ?? '.fields-group' }}',
                class: '.{{ $load['class'] }}',
                url: "{!! $load['url'].(strpos($load['url'],'?')?'&':'?') !!}q=",
                textField: "{{ $load['textField'] }}",
                idField: "{{ $load['idField'] }}",
            });
        });
        $(selector).trigger('change');
    </script>
    @endif
@overwrite
