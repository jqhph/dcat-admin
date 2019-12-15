/**
 * 行选择器
 *
 * @constructor
 */
LA.RowSelector = function RowSelector(opts) {
    opts = $.extend({
        checkbox: '', // checkbox css选择器
        selectAll: '', // 全选checkbox css选择器
        bg: 'rgba(255, 255,213,0.4)', // 选中效果颜色
        clickTr: false, // 点击行事件
    }, opts);

    var checkboxSelector = opts.checkbox,
        selectAllSelector = opts.selectAll,
        $ckb = $(checkboxSelector);

    $(selectAllSelector).on('change', function() {
        var cbx = $(checkboxSelector);

        for (var i = 0; i < cbx.length; i++) {
            if (this.checked && !cbx[i].checked) {
                cbx[i].click();
            } else if (!this.checked && cbx[i].checked) {
                cbx[i].click();
            }
        }
    });
    if (opts.clickTr) {
        $ckb.click(function (e) {
            if (typeof e.cancelBubble != "undefined") {
                e.cancelBubble = true;
            }
            if (typeof e.stopPropagation != "undefined") {
                e.stopPropagation();
            }
        }).parents('tr').click(function (e) {
            $(this).find(checkboxSelector).click();
        });
    }

    $ckb.on('change', function () {
        var tr = $(this).closest('tr');
        if (this.checked) {
            tr.css('background-color', opts.bg);
        } else {
            tr.css('background-color', '');
        }
    });

    this.getIds = function () {
        var selected = [];
        $(checkboxSelector+':checked').each(function() {
            selected.push($(this).data('id'));
        });

        return selected;
    };
    this.getRows = function () {
        var selected = [];
        $(checkboxSelector+':checked').each(function(){
            selected.push({'id': $(this).data('id'), 'label': $(this).data('label')})
        });

        return selected;
    };

    return this;
};