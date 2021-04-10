
export default class RowSelector {
    constructor(options) {
        let _this = this;

        _this.options = $.extend({
            // checkbox css选择器
            checkboxSelector: '',
            // 全选checkbox css选择器
            selectAllSelector: '',
            // 选中效果颜色
            background: 'rgba(255, 255,213,0.4)',
            // 点击行事件
            clickRow: false,
            // 表格选择器
            container: 'table',
        }, options);

        _this.init()
    }

    init() {
        let options = this.options,
            checkboxSelector = options.checkboxSelector,
            $document = $(document),
            selectAll = options.selectAllSelector;

        $(selectAll).on('change', function() {
            let checked = this.checked;

            $.each($(this).parents(options.container).find(checkboxSelector), function (_, checkbox) {
                let $this = $(checkbox);

                if (! $this.attr('disabled')) {
                    $this.prop('checked', checked).trigger('change');
                }
            });
        });
        if (options.clickRow) {
            $document.off('click', checkboxSelector).on('click', checkboxSelector, function (e) {
                if (typeof e.cancelBubble != "undefined") {
                    e.cancelBubble = true;
                }
                if (typeof e.stopPropagation != "undefined") {
                    e.stopPropagation();
                }
            });

            $document.off('click', options.container+' tr').on('click', options.container+' tr', function () {
                $(this).find(checkboxSelector).click();
            });
        }

        $document.off('change', checkboxSelector).on('change', checkboxSelector, function () {
            var tr = $(this).closest('tr');
            if (this.checked) {
                tr.css('background-color', options.background);

                if ($(checkboxSelector + ':checked').length === $(checkboxSelector).length) {
                    $(selectAll).prop('checked', true)
                }
            } else {
                tr.css('background-color', '');
            }
        });
    }

    /**
     * 获取选中的主键数组
     *
     * @returns {Array}
     */
    getSelectedKeys() {
        let selected = [];

        $(this.options.checkboxSelector+':checked').each(function() {
            var id = $(this).data('id');
            if (selected.indexOf(id) === -1) {
                selected.push(id);
            }
        });

        return selected;
    }

    /**
     * 获取选中的行数组
     *
     * @returns {Array}
     */
    getSelectedRows() {
        let selected = [];

        $(this.options.checkboxSelector+':checked').each(function() {
            var id = $(this).data('id'), i, exist;

            for (i in selected) {
                if (selected[i].id === id) {
                    exist = true
                }
            }

            exist || selected.push({'id': id, 'label': $(this).data('label')})
        });

        return selected;
    }
}
