(function (w, $) {
    let Dcat = w.Dcat;

    /**
     * 树状表格
     *
     * @param opts
     * @constructor
     */
    function Tree(opts) {
        this.options = $.extend({
            button: null,
            table: null,
            url: '',
            perPage: '',
            showNextPage: '',
            pageQueryName: '',
            parentIdQueryName: '',
            tierQueryName: '',
            showIcon: 'fa-angle-right',
            hideIcon: 'fa-angle-down',
            loadMoreIcon: '<i class="feather icon-more-horizontal"></i>',
        }, opts);

        this.key = this.tier = this.row = this.data = this._req = null;

        this._init();
    }

    Tree.prototype = {
        _init: function () {
            this._bindClick();
        },

        _bindClick: function () {
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
                _this.tier = $this.data('tier');
                _this.row = $this.closest('tr');

                if ($this.data('inserted') == '0') {
                    _this.request(1);
                    $this.data('inserted', 1);
                }

                _i.toggleClass(opts.showIcon + ' ' + opts.hideIcon);

                var children = [];

                getChildren(_this.row.nextAll(), _this.row).forEach(function (v) {
                    if (getTier(v) !== (_this.tier + 1)) {
                        return;
                    }

                    children.push(v);

                    shown ? $(v).show() : $(v).hide();
                });

                children.forEach(function (v) {
                    if (shown) {
                        return
                    }

                    var icon = $(v).find('a[data-tier=' + getTier(v) + '] i');

                    if (icon.hasClass(opts.hideIcon)) {
                        icon.parent().click();
                    }
                })
            })
        },

        request: function (page, after) {
            var _this = this,
                row = _this.row,
                key = _this.key,
                tier = _this.tier,
                tableSelector = _this.options.table;

            if (_this._req) {
                return;
            }
            _this._req = 1;
            Dcat.loading();

            var data = {};

            data[_this.options.parentIdQueryName] = key;
            data[_this.options.tierQueryName] = tier + 1;
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
                    var children = getChildren(row.nextAll(), row);
                    row = children.length ? $(children.pop()) : row;

                    var _body = $('<div>'+resp+'</div>'),
                        _tbody = _body.find(tableSelector + ' tbody'),
                        lastPage = _body.find('last-page').text(),
                        nextPage = _body.find('next-page').text();

                    // 标记子节点行
                    _tbody.find('tr').each(function (_, v) {
                        $(v).attr('data-tier', tier + 1)
                    });

                    if (
                        _this.options.showNextPage
                        && _tbody.find('tr').length == _this.options.perPage
                        && lastPage >= page
                    ) {
                        // 加载更多
                        let loadMore = $(
                            `<tr data-tier="${tier + 1}" data-page="${nextPage}">
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
    };

    /**
     * 可排序功能
     *
     * @param opts
     * @constructor
     */
    function Orderable(opts) {
        this.options = $.extend({
            button: null,
            url: '',
        }, opts);

        this.direction = this.key = this.tier = this.row = this._req = null;

        this.init();
    }

    Orderable.prototype = {
        init: function () {
            var _this = this;

            $(_this.options.button).off('click').click(function () {
                if (_this._req) {
                    return;
                }

                _this._req = 1;
                Dcat.loading();

                var $this = $(this);

                _this.key = $this.data('id');
                _this.direction = $this.data('direction');
                _this.row = $this.closest('tr');
                _this.tier = getTier(_this.row);

                _this.request();
            })
        },

        request: function () {
            var _this = this,
                key = _this.key,
                row = _this.row,
                tier = _this.tier,
                direction = _this.direction,
                prevAll = row.prevAll(),
                nextAll = row.nextAll(),
                prev = row.prevAll('tr').first(),
                next = row.nextAll('tr').first();

            $.put({
                url: _this.options.url.replace(':key', key),
                data: {_orderable: direction},
                success: function(data){
                    Dcat.loading(false);
                    _this._req = 0;
                    if (! data.status) {
                        return data.message && Dcat.warning(data.message);
                    }

                    Dcat.success(data.message);

                    if (direction) {
                        var prevRow = sibling(prevAll, tier);
                        if (swapable(prevRow, tier) && prev.length && getTier(prev) >= tier) {
                            prevRow.before(row);

                            // 把所有子节点上移
                            getChildren(nextAll, row).forEach(function (v) {
                                prevRow.before(v)
                            });
                        }
                    } else {
                        var nextRow = sibling(nextAll, tier),
                            nextRowChildren = nextRow ? getChildren(nextRow.nextAll(), nextRow) : [];

                        if (swapable(nextRow, tier) && next.length && getTier(next) >= tier) {
                            nextAll = row.nextAll();

                            if (nextRowChildren.length) {
                                nextRow = $(nextRowChildren.pop())
                            }

                            // 把所有子节点下移
                            var all = [];
                            getChildren(nextAll, row).forEach(function (v) {
                                all.unshift(v)
                            });

                            all.forEach(function(v) {
                                nextRow.after(v)
                            });

                            nextRow.after(row);
                        }
                    }
                },
                error: function (a, b, c) {
                    _this._req = 0;
                    Dcat.loading(false);
                    Dcat.handleAjaxError(a, b, c)
                }
            });
        },
    };

    /**
     * 异步加载表格
     *
     * @param options
     * @constructor
     */
    function AsyncTable(options) {
        options = $.extend({
            container: '.table-card',
        }, options)

        function load(url, box) {
            var $this = $(this);

            box = box || $this;

            url = $this.data('url') || url;
            if (! url) {
                return;
            }

            // 缓存当前请求地址
            box.attr('data-current', url);

            box.loading({background: 'transparent!important'});

            Dcat.helpers.asyncRender(url, function (html) {
                box.loading(false);
                box.html(html);
                bind(box);
                box.trigger('table:loaded');
            });
        }

        function bind(box) {
            function loadLink() {
                load($(this).attr('href'), box);

                return false;
            }

            box.find('.pagination .page-link').on('click', loadLink);
            box.find('.grid-column-header a').on('click', loadLink);

            box.find('form').on('submit', function () {
                load($(this).attr('action')+'&'+$(this).serialize(), box);

                return false;
            });

            box.find('.filter-box .reset').on('click', loadLink);

            box.find('.grid-selector a').on('click', loadLink);

            Dcat.ready(function () {
                setTimeout(function () {
                    box.find('.grid-refresh').off('click').on('click', function () {
                        load(box.data('current'), box);

                        return false;
                    })
                }, 10)
            })
        }

        $(options.container).on('table:load', load);
    }


    function isTr(v) {
        return $(v).prop('tagName').toLocaleLowerCase() === 'tr'
    }

    function getTier(v) {
        return parseInt($(v).data('tier') || 0);
    }

    function isChildren(parent, child) {
        return getTier(child) > getTier(parent);
    }

    function getChildren(all, parent) {
        var arr = [], isBreak = false, firstTr;
        all.each(function (_, v) {
            // 过滤非tr标签
            if (! isTr(v) || isBreak) return;

            firstTr || (firstTr = $(v));

            // 非连续的子节点
            if (firstTr && ! isChildren(parent, firstTr)) {
                return;
            }

            if (isChildren(parent, v)) {
                arr.push(v)
            } else {
                isBreak = true;
            }
        });

        return arr;
    }

    function swapable(_o, tier) {
        if (
            _o
            && _o.length
            && tier === getTier(_o)
        ) {
            return true
        }
    }

    function sibling(all, tier) {
        var next;

        all.each(function (_, v) {
            if (getTier(v) === tier && ! next && isTr(v)) {
                next = $(v);
            }
        });

        return next;
    }

    Dcat.grid.Tree = function (opts) {
        return new Tree(opts);
    };
    Dcat.grid.Orderable = function (opts) {
        return new Orderable(opts);
    };
    Dcat.grid.AsyncTable =function (opts) {
        return new AsyncTable(opts)
    }
})(window, jQuery);