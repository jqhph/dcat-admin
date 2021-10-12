<script>
@section('admin.select-ajax')
    @if(isset($ajax))
        var _this = $('{{ $selector }}');
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
    @if(isset($depends))
    _this.data('selectConfigs', $.extend({}, configs));
    delete configs.ajax;
    @endif
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

@if(isset($depends) && isset($ajax))
{{--depends联动--}}
<script once>
    var _this = $('{!! $selector !!}');
    var fields = {!! $depends['fields'] !!};
    var form = _this.closest('form');

    var getFormFieldsVal = function(form, fields){
        var formData = $(form).serializeArray();
        var params = {};

        for (var field of fields) {
            for (var data of formData) {
                if (!data.value.length) {
                    continue;
                }
                if (data.name === field) {
                    params[field] = data.value
                }else if (data.name === field + '[]') {
                    if(!Array.isArray(params[field])){
                        params[field] = []
                    }
                    params[field].push(data.value);
                }
            }
            if (!params.hasOwnProperty(field)){
                return false;
            }
        }
        return params;
    }

    var getSourceUrl = function(url, params){
        return url + (url.match(/\?/)?'&':'?') + (new URLSearchParams(params)).toString();
    }

    @if($depends['clear'])
    $.map(fields, function (field) {
        var _selectors = [
            '[name="' + field + '"]',
            '[name="' + field + '[]"]'
        ];
        $.map(_selectors, function(_selector){
            form.off('change.depends', _selector)
                .on('change.depends', _selector, function () {
                    _this.val(null).trigger('change');

                    var params = getFormFieldsVal(form, fields);
                    var configs = $.extend({}, _this.data('selectConfigs'));

                    configs.ajax = !params ? {} : $.extend(configs.ajax, { url: getSourceUrl("{{ $ajax['url'] }}", params) });

                    _this.select2(configs)
                });

            form.find(_selector).trigger('change.depends');
        })
    });
    @endif
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
