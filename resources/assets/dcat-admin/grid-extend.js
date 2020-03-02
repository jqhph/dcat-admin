(function (w, $) {
    function Tree(opts) {
        this.options = $.extend({
            button: null,
            table: null,
            url: '',
            perPage: '',
            showNextPage: '',
            pageQueryName: '',
            parentIdQueryName: '',
            levelQueryName: '',
            showIcon: 'fa-angle-right',
            hideIcon: 'fa-angle-down',
            loadMoreText: '<svg style="fill:currentColor" t="1582877365167" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="32874" width="24" height="24"><path d="M162.8 515m-98.3 0a98.3 98.3 0 1 0 196.6 0 98.3 98.3 0 1 0-196.6 0Z" p-id="32875"></path><path d="M511.9 515m-98.3 0a98.3 98.3 0 1 0 196.6 0 98.3 98.3 0 1 0-196.6 0Z" p-id="32876"></path><path d="M762.8 515a98.3 98.3 0 1 0 196.6 0 98.3 98.3 0 1 0-196.6 0Z" p-id="32877"></path></svg>',
        }, opts);

        this.key = this.level = this.row = this.data = this._req = null;

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
                _this.level = $this.data('level');
                _this.row = $this.closest('tr');

                if ($this.data('inserted') == '0') {
                    _this._request(1);
                    $this.data('inserted', 1);
                }

                _i.toggleClass(opts.showIcon + ' ' + opts.hideIcon);

                var children = [];

                getChildren(_this.row.nextAll(), _this.row).forEach(function (v) {
                    if (! (getLevel(v) === (_this.level + 1))) {
                        return;
                    }

                    children.push(v);

                    shown ? $(v).show() : $(v).hide();
                });

                children.forEach(function (v) {
                    if (shown) {
                        return
                    }

                    var icon = $(v).find('a[data-level=' + getLevel(v) + '] i');

                    if (icon.hasClass(opts.hideIcon)) {
                        icon.parent().click();
                    }
                })
            })
        },

        _request: function (page, after) {
            var _this = this,
                row = _this.row,
                key = _this.key,
                level = _this.level,
                tableSelector = _this.options.table;

            if (_this._req) {
                return;
            }
            _this._req = 1;
            LA.loading();

            var data = {
                _token: LA.token,
            };

            data[_this.options.parentIdQueryName] = key;
            data[_this.options.levelQueryName] = level + 1;
            data[_this.options.pageQueryName.replace(':key', key)] = page;

            $.ajax({
                url: _this.options.url,
                type: 'GET',
                data: data,
                headers: {'X-PJAX': true},
                success: function (resp) {
                    after && after();
                    LA.loading(false);
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
                        $(v).attr('data-level', level + 1)
                    });

                    if (
                        _this.options.showNextPage
                        && _tbody.find('tr').length == _this.options.perPage
                        && lastPage >= page
                    ) {
                        // 加载更多
                        var loadMore = $("<tr data-level='" + (level + 1) + "' data-page='" + nextPage
                            + "'><td colspan='"+(row.find('td').length)
                            + "' align='center' style='cursor: pointer'> <a>" + _this.options.loadMoreText + "</a> </td></tr>");

                        row.after(loadMore);

                        // 加载更多
                        loadMore.click(function () {
                            var _t = $(this);
                            _this._request(_t.data('page'), function () {
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
                    $(document).trigger('pjax:script')
                },
                error:function(a, b, c){
                    after && after();
                    LA.loading(false);
                    _this._req = 0;
                    if (a.status != 404) {
                        LA.ajaxError(a, b, c);
                    }
                }
            });
        }
    };

    function Orderable(opts) {
        this.options = $.extend({
            button: null,
            url: '',
        }, opts);

        this.direction = this.key = this.level = this.row = this._req = null;

        this._init();
    }

    Orderable.prototype = {
        _init: function () {
            this._bindClick()
        },

        _bindClick: function () {
            var _this = this;

            $(_this.options.button).off('click').click(function () {
                if (_this._req) {
                    return;
                }

                _this._req = 1;
                LA.loading();

                var $this = $(this);

                _this.key = $this.data('id');
                _this.direction = $this.data('direction');
                _this.row = $this.closest('tr');
                _this.level = getLevel(_this.row);

                _this._request();
            })
        },

        _request: function () {
            var _this = this,
                key = _this.key,
                row = _this.row,
                level = _this.level,
                direction = _this.direction,
                prevAll = row.prevAll(),
                nextAll = row.nextAll(),
                prev = row.prevAll('tr').first(),
                next = row.nextAll('tr').first();

            $.ajax({
                type: 'POST',
                url: _this.options.url.replace(':key', key),
                data: {_method:'PUT', _token:LA.token, _orderable:direction},
                success: function(data){
                    LA.loading(false);
                    _this._req = 0;
                    if (! data.status) {
                        return data.message && LA.warning(data.message, 'rt');
                    }

                    LA.success(data.message);

                    if (direction) {
                        var prevRow = sibling(prevAll, level);
                        if (swapable(prevRow, level) && prev.length && getLevel(prev) >= level) {
                            prevRow.before(row);

                            // 把所有子节点上移
                            getChildren(nextAll, row).forEach(function (v) {
                                prevRow.before(v)
                            });
                        }
                    } else {
                        var nextRow = sibling(nextAll, level),
                            nextRowChildren = nextRow ? getChildren(nextRow.nextAll(), nextRow) : [];

                        if (swapable(nextRow, level) && next.length && getLevel(next) >= level) {
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
                    LA.loading(false);
                    LA.ajaxError(a, b, c)
                }
            });
        },
    };

    function isTr(v) {
        return $(v).prop('tagName').toLocaleLowerCase() === 'tr'
    }

    function getLevel(v) {
        return parseInt($(v).data('level') || 0);
    }

    function isChildren(parent, child) {
        return getLevel(child) > getLevel(parent);
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

    function swapable(_o, level) {
        if (
            _o
            && _o.length
            && level === getLevel(_o)
        ) {
            return true
        }
    }

    function sibling(all, level) {
        var next;

        all.each(function (_, v) {
            if (getLevel(v) === level && ! next && isTr(v)) {
                next = $(v);
            }
        });

        return next;
    }

    w.LA.grid.tree = function (opts) {
        return new Tree(opts);
    };
    w.LA.grid.orderable = function (opts) {
        return new Orderable(opts);
    };
})(window, $);