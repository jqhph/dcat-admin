<a href="javascript:void(0)" class="{{ $prefix }}-open-tree" data-checked="{{ $checkAll }}" data-val="{{ $value }}">
    <i class='feather icon-align-right'></i> {{ trans('admin.view') }}
</a>

<script require="@jstree">
    var selector = '.{{ $prefix}}-open-tree';

    $(document).off('click', selector).on('click', selector, function () {
        var tpl = '<div class="jstree-wrapper p-1" style="border:0"><div class="da-tree" style="margin-top:10px"></div></div>',
            url = '{{ $url }}',
            t = $(this),
            val = t.data('val'),
            ckall = t.data('checked'),
            idx,
            loading,
            opts = {!! json_encode($options) !!};

        val = val ? String(val).split(',') : [];

        if (url) {
            if (loading) return;
            loading = 1;

            t.buttonLoading();
            $.ajax(url, {data: {value: val}}).then(function (resp) {
                loading = 0;
                t.buttonLoading(false);

                if (!resp.status) {
                    return Dcat.error(resp.message || '系统繁忙，请稍后再试');
                }

                build(resp.value);
            });
        } else {
            build(val);
        }

        function build(val) {
            opts.core.data = formatNodes(val, {!! json_encode($nodes) !!});

            idx = layer.open({
                type: 1,
                area: {!! json_encode($area) !!},
                content: tpl,
                title: '{{ $title }}',
                success: function (a, idx) {
                    var tree = $('#layui-layer'+idx).find('.da-tree');

                    tree.on("loaded.jstree", function () {
                        tree.jstree('open_all');
                    }).jstree(opts);
                }
            });

            $(document).one('pjax:complete', function () {
                layer.close(idx);
            });
        }

        function formatNodes(value, all) {
            var idColumn = '{{ $columnNames['id'] }}',
                textColumn = '{{ $columnNames['text'] }}',
                parentColumn = '{{ $columnNames['parent'] }}',
                parentIds = [], nodes = [], i, v, parentId;

            for (i in all) {
                v = all[i];
                if (!v[idColumn]) continue;

                parentId = v[parentColumn] || '#';
                if (!parentId) {
                    parentId = '#';
                } else {
                    parentIds.push(parentId);
                }

                v['state'] = {'disabled': true};

                if (ckall || (value && Dcat.helpers.inObject(value, v[idColumn]))) {
                    v['state']['selected'] = true;
                }

                nodes.push({
                    'id'     : v[idColumn],
                    'text'   : v[textColumn] || null,
                    'parent' : parentId,
                    'state'  : v['state'],
                });
            }

            return nodes;
        }
    });
</script>