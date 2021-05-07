
let defaultName = '_def_';

export default class Grid {
    constructor(Dcat) {
        Dcat.grid = this;

        this.selectors = {};
    }

    // 添加行选择器对象
    addSelector(selector, name) {
        this.selectors[name || defaultName] = selector
    }

    // 获取行选择器选中的ID字符串
    selected(name) {
        return this.selectors[name || defaultName].getSelectedKeys()
    }

    // 获取行选择器选中的行
    selectedRows(name) {
        return this.selectors[name || defaultName].getSelectedRows()
    }

    async(options) {
        return new AsyncGrid(options);
    }
}

class AsyncGrid {
    constructor(options) {
        options = $.extend({
            selector: null,
            bodySelector: '.async-body',
            tableSelector: '.async-table',
            queryName: null,
            url: null,
            loadingStyle: 'height:240px;',
        }, options);

        var self = this,
            $box = $(options.selector),
            $body = $box.find(options.bodySelector);

        self.options = options;
        self.$box = $box;
        self.$body = $body;
        self.loading = false;
    }

    render(url) {
        let self = this, options = self.options;

        url = url || options.url;

        if (self.loading || url.indexOf('javascript:') !== -1) {
            return;
        }
        self.loading = true;

        let $box = self.$box,
            $body = self.$body,
            reqName = options.queryName,
            tableSelector = options.tableSelector;

        // loading效果
        let loadingOptions = {background: 'transparent'}
        if ($body.find(`${tableSelector} tbody tr`).length <= 2) {
            loadingOptions['style'] = options.loadingStyle;
        }
        $body.find(tableSelector).loading(loadingOptions);
        Dcat.NP.start();

        if (url.indexOf('?') === -1) {
            url += '?';
        }

        if (url.indexOf(reqName) === -1) {
            url += '&'+reqName+'=1';
        }

        history.pushState({}, '', url.replace(reqName+'=1', ''));

        $box.data('current', url);

        Dcat.helpers.asyncRender(url, function (html) {
            self.loading = false;
            Dcat.NP.done();

            $body.html(html);

            let refresh = function () {
                self.render($box.data('current'));
            };

            // 表格渲染事件
            $body.off('grid:render').on('grid:render', refresh);
            $body.find('table').on('grid:render', refresh);

            // 刷新按钮
            $box.find('.grid-refresh').off('click').on('click', function () {
                refresh();

                return false;
            });

            // 分页
            $box.find('.pagination .page-link').on('click', loadLink);
            // 页选择器
            $box.find('.per-pages-selector .dropdown-item a').on('click', loadLink);
            // 表头url
            $box.find('.grid-column-header a').on('click', loadLink);

            // 快捷搜索、表头搜索以及过滤器筛选
            $box.find('form').off('submit').on('submit', function () {
                var action = $(this).attr('action');

                if ($(this).attr('method') === 'post') {
                    return;
                }

                if (action.indexOf('?') === -1) {
                    action += '?';
                }

                self.render(action+'&'+$(this).serialize());

                return false;
            });

            $box.find('.filter-box .reset').on('click', loadLink);

            // 规格选择器
            $box.find('.grid-selector a').on('click', loadLink);
        });

        function loadLink() {
            self.render($(this).attr('href'));

            return false;
        }
    }
}
