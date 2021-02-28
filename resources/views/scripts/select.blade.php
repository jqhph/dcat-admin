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
            var values = [];

            $(this).find('option:selected').each(function () {
                if (String(this.value) === '0'|| this.value) {
                    values.push(this.value)
                }
            });

            if (! values.length) {
                return;
            }
            target.find("option").remove();

            Dcat.loading();

            $.ajax("{!! $load['url'].(strpos($load['url'],'?')?'&':'?') !!}q="+values.join(',')).then(function (data) {
                Dcat.loading(false);

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
