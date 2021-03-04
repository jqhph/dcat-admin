(function (w) {
    function SelectTable(options) {
        options = $.extend({
            dialog: null,
            container: null,
            input: null,
            button: '.submit-btn',
            cancel: '.cancel-btn',
            table: '.async-table',
            multiple: false,
            max: 0,
            values: [],
            lang: {
                exceed_max_item: Dcat.lang.exceed_max_item || '已超出最大可选择的数量',
            },
        }, options);

        let self = this;

        self.options = options;
        self.$input = $(options.input);
        self.selected = {}; // 保存临时选中的ID

        self.init();
    }

    SelectTable.prototype = {
        init() {
            let self = this,
                options = self.options,
                values = options.values;

            self.labels = {};

            for (let i in values) {
                self.labels[values[i]['id']] = values[i]['label']
            }

            // 保存临时选中的值
            self.resetSelected();

            $(document).on('dialog:shown', options.dialog, function () {
                self.$dialog = $(options.dialog);
                self.$button = self.$dialog.find(options.button);
                self.$cancel = self.$dialog.find(options.cancel);

                // 提交按钮
                self.$button.on('click', function () {
                    var selected = self.getSelectedRows();

                    self.setKeys(selected[1]);

                    self.render(selected[0]);

                    self.$dialog.trigger('dialog:close');

                    // 重置已选中数据
                    self.resetSelected();
                });

                // 取消按钮
                self.$cancel.on('click', function () {
                    self.$dialog.trigger('dialog:close');
                });

                // 绑定相关事件
                self.bind();

                // 重置已选中数据
                self.resetSelected();
            });

            // 渲染选中的数据
            self.render(values);
        },

        bind() {
            let self = this,
                options = self.options;

            // 表格加载完成事件
            self.$dialog.find(options.table).on('table:loaded', function () {
                let checkbox = self.getCheckbox();

                if (! options.multiple) {
                    // 移除全选按钮
                    $(this).find('.checkbox-grid-header').remove();
                }

                checkbox.on('change', function () {
                    let $this = $(this),
                        id = $this.data('id'),
                        label = $this.data('label');

                    if (this.checked) {
                        if (! options.multiple) {
                            self.selected = {};
                        }
                        self.selected[id] = {id: id, label: label};

                        // 多选
                        if (options.max && (self.getSelectedRows()[0].length > options.max)) {
                            $this.prop('checked', false);
                            delete self.selected[id];

                            return Dcat.warning(self.options.lang.exceed_max_item);
                        }
                    } else {
                        delete self.selected[id];
                    }

                    if (! options.multiple) {
                        if (this.checked) {
                            // 单选效果
                            checkbox.each(function () {
                                let $this = $(this);

                                if ($this.data('id') != id) {
                                    $this.prop('checked', false);
                                    $this.parents('tr').css('background-color', '');
                                }
                            });
                        }
                    }
                });

                // 选中默认选项
                checkbox.each(function () {
                    let $this = $(this),
                        current = $this.data('id');

                    // 保存label字段
                    self.labels[current] = $this.data('label');

                    for (let i in self.selected) {
                        if (current == i) {
                            $this.prop('checked', true).trigger('change');

                            continue;
                        }
                    }

                    $this.trigger('change');
                });
            })
        },

        // 重置已选中数据
        resetSelected() {
            let self = this,
                keys = self.getKeys();

            self.selected = {};

            for (let i in keys) {
                self.selected[keys[i]] = {id: keys[i], label: self.labels[keys[i]]};
            }
        },

        getCheckbox() {
            return this.$dialog.find('.checkbox-grid-column input[type="checkbox"]');
        },

        getSelectedRows() {
            let self = this,
                selected = [],
                ids = [];

            for (let i in self.selected) {
                if (! self.selected[i]) {
                    continue;
                }

                ids.push(i);
                selected.push(self.selected[i])
            }

            return [selected, ids];
        },

        render(selected) {
            let self = this,
                options = self.options,
                box = $(options.container),
                placeholder = box.find('.default-text'),
                option = box.find('.option');

            if (! selected || ! selected.length) {
                placeholder.removeClass('d-none');
                option.addClass('d-none');

                if (options.multiple) {
                    box.addClass('form-control');
                }

                return;
            }

            placeholder.addClass('d-none');
            option.removeClass('d-none');

            if (! options.multiple) {
                return renderDefault(selected, self, options);
            }

            return renderMultiple(selected, self, options);
        },

        setKeys(keys) {
            // 手动触发change事件，方便监听值变化
            this.$input.val(keys.length ? keys.join(',') : '').trigger('change');
        },

        deleteKey(key) {
            let val = this.getKeys(),
                results = [];

            for (let i in val) {
                if (val[i] != key) {
                    results.push(val[i])
                }
            }

            this.setKeys(results)
        },

        getKeys() {
            let val = this.$input.val();

            if (! val) return [];

            return String(val).split(',');
        },
    };

    // 多选
    function renderMultiple(selected, self, options) {
        let html = [],
            box = $(options.container),
            placeholder = box.find('.default-text'),
            option = box.find('.option');

        if (! box.hasClass('select2')) {
            box.addClass('select2 select2-container select2-container--default select2-container--below');
        }
        box.removeClass('form-control');

        for (let i in selected) {
            html.push(`<li class="select2-selection__choice" >
    ${selected[i]['label']} <span data-id="${selected[i]['id']}" class="select2-selection__choice__remove remove " role="presentation"> ×</span>
</li>`);
        }

        html.unshift('<span class="select2-selection__clear remove-all">×</span>');

        html = `<span class="select2-selection select2-selection--multiple">
 <ul class="select2-selection__rendered">${html.join('')}</ul>
 </span>`;

        var $tags = $(html);

        option.html($tags);

        $tags.find('.remove').on('click', function () {
            var $this = $(this);

            self.deleteKey($this.data('id'));

            $this.parent().remove();

            if (! self.getKeys().length) {
                removeAll();
            }
        });

        function removeAll() {
            option.html('');
            placeholder.removeClass('d-none');
            option.addClass('d-none');

            box.addClass('form-control');

            self.setKeys([]);
        }

        $tags.find('.remove-all').on('click', removeAll);
    }

    // 单选
    function renderDefault(selected, self, options) {
        let box = $(options.container),
            placeholder = box.find('.default-text'),
            option = box.find('.option');

        var remove = $("<div class='pull-right ' style='font-weight:bold;cursor:pointer'>×</div>");

        option.text(selected[0]['label']);
        option.append(remove);

        remove.on('click', function () {
            self.setKeys([]);
            placeholder.removeClass('d-none');
            option.addClass('d-none');
        });
    }

    Dcat.grid.SelectTable = function (opts) {
        return new SelectTable(opts)
    };
})(window)