/**
 * 行排序
 */

export default class Orderable {
    constructor(Helper, opts) {
        this.options = $.extend({
            button: null,
            url: '',
        }, opts);

        this.helper = Helper;

        this.direction = this.key = this.depth = this.row = this._req = null;

        this._init();
    }

    _init() {
        let _this = this;

        $(_this.options.button).off('click').click(function () {
            if (_this._req) {
                return;
            }

            _this._req = 1;
            Dcat.loading();

            let $this = $(this);

            _this.key = $this.data('id');
            _this.direction = $this.data('direction');
            _this.row = $this.closest('tr');
            _this.depth = _this.helper.getDepth(_this.row);

            _this.request();
        })
    }

    request() {
        var _this = this,
            helper = _this.helper,
            key = _this.key,
            row = _this.row,
            depth = _this.depth,
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
                    return data.data.message && Dcat.warning(data.data.message);
                }

                Dcat.success(data.data.message);

                if (direction) {
                    var prevRow = helper.sibling(prevAll, depth);
                    if (helper.swapable(prevRow, depth) && prev.length && helper.getDepth(prev) >= depth) {
                        prevRow.before(row);

                        // 把所有子节点上移
                        helper.getChildren(nextAll, row).forEach(function (v) {
                            prevRow.before(v)
                        });
                    }
                } else {
                    var nextRow = helper.sibling(nextAll, depth),
                        nextRowChildren = nextRow ? helper.getChildren(nextRow.nextAll(), nextRow) : [];

                    if (helper.swapable(nextRow, depth) && next.length && helper.getDepth(next) >= depth) {
                        nextAll = row.nextAll();

                        if (nextRowChildren.length) {
                            nextRow = $(nextRowChildren.pop())
                        }

                        // 把所有子节点下移
                        var all = [];
                        helper.getChildren(nextAll, row).forEach(function (v) {
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
    }
}