/**
 * 树形表格
 */

export default class Tree {
    constructor(Helper, opts) {
        this.options = $.extend({
            button: null,
            table: null,
            url: '',
            perPage: '',
            showNextPage: '',
            pageQueryName: '',
            parentIdQueryName: '',
            depthQueryName: '',
            showIcon: 'fa-angle-right',
            hideIcon: 'fa-angle-down',
            loadMoreIcon: '<i class="feather icon-more-horizontal"></i>',
        }, opts);

        this.helper = Helper;

        this.key = this.depth = this.row = this.data = this._req = null;

        this._init();
    }

    // 绑定点击事件
    _init () {
        var _this = this,
            opts = _this.options;

        $(opts.button).off('click').click(function () {
            if (_this._req) {
                return;
            }

            var $this = $(this),
                _i = $("i", this),
                shown = _i.hasClass(opts.showIcon);

            _this.key = $this.data('key');
            _this.depth = $this.data('depth');
            _this.row = $this.closest('tr');

            if ($this.data('inserted') == '0') {
                _this.request(1);
                $this.data('inserted', 1);
            }

            _i.toggleClass(opts.showIcon + ' ' + opts.hideIcon);

            var children = [];

            _this.helper.getChildren(_this.row.nextAll(), _this.row).forEach(function (v) {
                if (_this.helper.getDepth(v) !== (_this.depth + 1)) {
                    return;
                }

                children.push(v);

                shown ? $(v).show() : $(v).hide();
            });

            children.forEach(function (v) {
                if (shown) {
                    return
                }

                var icon = $(v).find('a[data-depth=' + _this.helper.getDepth(v) + '] i');

                if (icon.hasClass(opts.hideIcon)) {
                    icon.parent().click();
                }
            })
        })
    }

    // 发起请求
    request (page, after) {
        var _this = this,
            row = _this.row,
            key = _this.key,
            depth = _this.depth,
            tableSelector = _this.options.table;

        if (_this._req) {
            return;
        }
        _this._req = 1;
        Dcat.loading();

        var data = {};

        data[_this.options.parentIdQueryName] = key;
        data[_this.options.depthQueryName] = depth + 1;
        data[_this.options.pageQueryName.replace(':key', key)] = page;

        $.ajax({
            url: _this.options.url,
            type: 'GET',
            data: data,
            headers: {'X-PJAX': true},
            success: function (resp) {
                after && after();
                Dcat.loading(false);
                _this._req = 0;

                // 获取最后一行
                var children = _this.helper.getChildren(row.nextAll(), row);
                row = children.length ? $(children.pop()) : row;

                var _body = $('<div>'+resp+'</div>'),
                    _tbody = _body.find(tableSelector + ' tbody'),
                    lastPage = _body.find('last-page').text(),
                    nextPage = _body.find('next-page').text();

                // 标记子节点行
                _tbody.find('tr').each(function (_, v) {
                    $(v).attr('data-depth', depth + 1)
                });

                if (
                    _this.options.showNextPage
                    && _tbody.find('tr').length == _this.options.perPage
                    && lastPage >= page
                ) {
                    // 加载更多
                    let loadMore = $(
                        `<tr data-depth="${depth + 1}" data-page="${nextPage}">
                                <td colspan="${row.find('td').length}" align="center" style="cursor: pointer">
                                    <a href="#" style="font-size: 1.5rem">${_this.options.loadMoreIcon}</a>
                                </td>
                            </tr>`
                    );

                    row.after(loadMore);

                    // 加载更多
                    loadMore.click(function () {
                        var _t = $(this);
                        _this.request(_t.data('page'), function () {
                            _t.remove();
                        });
                    });
                }

                // 附加子节点
                row.after(_tbody.html());

                // 附加子节点js脚本以及触发子节点js脚本执行
                _body.find('script').each(function (_, v) {
                    row.after(v);
                });

                // 附加的HTML
                $('body .extra-html').append(_body.find('.extra-html').html());

                // 主动触发ready事件，执行子节点附带的js脚本
                Dcat.triggerReady();
            },
            error:function(a, b, c){
                after && after();

                Dcat.loading(false);

                _this._req = 0;

                if (a.status != 404) {
                    Dcat.handleAjaxError(a, b, c);
                }
            }
        });
    }
}
