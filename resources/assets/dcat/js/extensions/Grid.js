
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
        let nullFun = function () {};

        options = $.extend({
            selector: null,
            bodySelector: '.async-body',
            tableSelector: '.async-table',
            queryName: null,
            url: null,
            loadingStyle: 'height:240px;',
            before: nullFun,
            after: nullFun,
        }, options);

        var self = this,
            $box = $(options.selector),
            $body = $box.find(options.bodySelector);

        self.options = options;
        self.$box = $box;
        self.$body = $body;
        self.loading = false;
    }

    render(url, callback) {
        let self = this, options = self.options;

        url = url || options.url;

        if (self.loading || url.indexOf('javascript:') !== -1) {
            return;
        }
        self.loading = true;

        let $box = self.$box,
            $body = self.$body,
            reqName = options.queryName,
            tableSelector = options.tableSelector,
            $table = $body.find(tableSelector),
            events = {0: 'grid:rendering', 1: 'grid:render', 2: 'grid:rendered'},
            before = options.before,
            after = options.after;

        // 开始渲染前事件
        before($box, url);
        $box.trigger(events[0], [url]);
        $body.trigger(events[0], [url]);

        // loading效果
        let loadingOptions = {background: 'transparent'}
        if ($body.find(`${tableSelector} tbody tr`).length <= 2) {
            loadingOptions['style'] = options.loadingStyle;
        }
        $table.loading(loadingOptions);
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
            $box.off(events[1]).on(events[1], refresh);
            $body.off(events[1]).on(events[1], refresh);
            $table.on(events[1], refresh);

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

            // 渲染完成后事件
            $box.trigger(events[2], [url, html]);
            $body.trigger(events[2], [url, html]);
            $table.trigger(events[2], [url, html]);

            after($box, url, html);

            callback && callback($box, url, html);
        });

        function loadLink() {
            self.render($(this).attr('href'));

            return false;
        }
    }
}
