<style>
    .autocomplete-suggestions { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
    .autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
    .autocomplete-no-suggestion { padding: 2px 5px;}
    .autocomplete-selected { background: #F0F0F0; }
    .autocomplete-suggestions strong { font-weight: bold; color: #000; }
    .autocomplete-group { padding: 2px 5px; font-weight: bold; font-size: 16px; color: #000; display: block; border-bottom: 1px solid #000; }
</style>

@include('admin::form.input')

<script init="{!! $selector !!}" require="@autocomplete">

    var configs = {!! $configs !!};

    @if(isset($ajax))

    configs = $.extend(configs, {

        serviceUrl: '{{ $ajax['url'] }}',
        groupBy: '{{ $ajax['groupField'] }}',
        dataType: 'json',
        transformResult: function (response) {
            return {
                suggestions: (function (data, valueField) {
                    if (!data) {
                        return [];
                    }

                    if (valueField) {
                        return $.map(data, function (dat) {
                            return {value: Dcat.helpers.get(dat, valueField) + '', data: dat};
                        });
                    }

                    return data;
                })(response.data, '{{ $ajax['valueField'] }}')
            };
        }
    });
    @else
    configs = $.extend(configs, {
        lookup: {!! $options !!}
    });
    @endif

    @if(isset($depends))

    var fields = {!! $depends['fields'] !!};

    configs = $.extend(configs, {
        'onSearchStart': function (params) {

            var formData = $this.closest('form').serializeArray();

            var p = {};

            for (var field of fields) {
                for (var data of formData) {
                    if (!data.value.length){
                        continue;
                    }
                    if (data.name === field) {
                        p[field] = data.value
                    }
                    if (data.name === field + '[]') {
                        if(!Array.isArray(p[field])){
                            p[field] = []
                        }
                        p[field].push(data.value);
                    }
                }
                if (!p.hasOwnProperty(field)){
                    return false;
                }
            }

            params = $.extend(params, p);
        }
    })

    @if($depends['clear'])
    $.map(fields, function (field) {
        var _selectors = [
            '[name="' + field + '"]',
            '[name="' + field + '[]"]'
        ];
        $.map(_selectors, function(_selector){
            $this.closest('form').off('change.depends', _selector)
                .on('change.depends', _selector, function () {
                    $this.val('');
                });
        })
    });
    @endif

    @endif

    $this.autocomplete(configs);
</script>
