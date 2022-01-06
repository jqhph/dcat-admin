<a href="javascript:void(0)" class="grid-dialog-tree"
   data-url="{{ $url }}"
   data-title="{{ $title }}"
   data-checked="{{ $checkAll }}"
   data-val="{{ $value }}">
    <i class='feather icon-align-right'></i> {{ trans('admin.view') }}
</a>

<template>
    <template id="dialog-tree-tpl">
        <div class="jstree-wrapper p-1" style="border:0"><div class="da-tree" style="margin-top:10px"></div></div>
    </template>
</template>

<script require="@jstree" once>
    window.resolveDialogTree = function (options) {
        var tpl = $('#dialog-tree-tpl').html(),
            t = $(this),
            val = t.data('val'),
            url = t.data('url'),
            title = t.data('title'),
            ckall = t.data('checked'),
            idx,
            loading;

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

                open(resp.value);
            });
        } else {
            open(val);
        }

        function open(val) {
            options.config.core.data = formatNodes(val, options.nodes);

            idx = layer.open({
                type: 1,
                area: options.area,
                content: tpl,
                title: title,
                success: function (a, idx) {
                    var tree = $('#layui-layer'+idx).find('.da-tree');

                    tree.on("loaded.jstree", function () {
                        tree.jstree('open_all');
                    }).jstree(options.config);
                }
            });

            $(document).one('pjax:complete', function () {
                layer.close(idx);
            });
        }

        function formatNodes(value, all) {
            var idColumn = options.columns.id,
                textColumn = options.columns.text,
                parentColumn = options.columns.parent,
                nodes = [], i, v, parentId;

            for (i in all) {
                v = all[i];
                if (!v[idColumn]) continue;

                parentId = v[parentColumn] || '#';
                if (!parentId || parentId == options.rootParentId || parentId == '0') {
                    parentId = '#';
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
    }
</script>

<script require="@jstree">
    var nodes = {!! json_encode($nodes) !!};
    var options = {!! admin_javascript_json($options) !!};
    var area = {!! json_encode($area) !!};

    $('.grid-dialog-tree').off('click').on('click', function () {
        resolveDialogTree.call(this, {config: options, nodes: nodes, area: area, rootParentId: '{!! $rootParentId !!}', columns: {!! json_encode($columnNames) !!}});
    });
</script>
