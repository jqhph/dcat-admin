(function (w) {
    var NONE = '';

    function ResourceSelector(options) {
        options = $.extend({
            title: '选择', // 弹窗标题
            selector: '', // 选择按钮选择器
            column: '', // 字段名称
            source: '', // 资源地址
            maxItem: 1, // 最大选项数量，0为不限制
            area: ['80%', '90%'],
            items: {}, // 默认选中项，key => value 键值对
            placeholder: '', // input placeholder
            showCloseButton: false,
            closeButtonText: '关闭',
            exceedMaxItemTip: '您已超出最大可选择的数量',
            selectedOptionsTip: '已选中:num个选项',
            $displayerContainer: null, // 选项展示容器dom对象
            $hiddenInput: null, // 隐藏表单dom对象
            displayer: null, // 自定义选中项渲染方法
            disabled: false,
            clearAllClass: '',
            clearOneClass: '',
            window: null,
        }, options);

        options.window = options.window || (top || w);

        var self = ResourceSelector,
            column = options.column,
            cls = column.replace(/[\[\]]*/g, '') + (Math.random().toString(36).substr(2)),
            layer = options.window.layer,
            $input = options.$displayerContainer || $(options.selector).parents('.select-resource').find('div[name="' + column + '"]'),
            $hidden = options.$hiddenInput || $('input[name="' + column + '"]'),
            tagClearClass = options.clearOneClass || (cls + '-tag-clear-button'),
            clearClass = options.clearAllClass || (cls + '-clear-button'),
            maxItem = options.maxItem,
            originalItems = options.items,
            iframeWin,
            layerIdx,
            $layerWin;

        options.clearOneClass = tagClearClass;
        options.clearAllClass = clearClass;

        $(options.selector).click(function () {
            if (options.disabled) return;
            if (layerIdx) {
                $layerWin.show();
                click_checked_items();
                return;
            }
            $(document).one('pjax:complete', function () {// 跳转新页面时移除弹窗
                layer.close(layerIdx);
                $layerWin.remove();
                layerIdx = $layerWin = null;
            });

            layerIdx = layer.open({
                type: 2,
                title: options.title,
                shadeClose: true,
                maxmin: false,
                shade: false,
                skin: 'select-resource',
                area: format_area(options.area),
                content: options.source + '?_mini=1',
                btn: options.showCloseButton ? [options.closeButtonText] : null,
                success: function (layero) {
                    iframeWin = options.window[layero.find('iframe')[0]['name']];

                    // 绑定勾选默认选项事件
                    bind_checked_default_event(iframeWin);
                },
                yes: function () {
                    $layerWin.hide();
                    return false;
                },
                cancel: function () {
                    $layerWin.hide();
                    return false;
                }
            });

            $layerWin = options.window.$('#layui-layer' + layerIdx);

        });

        /**
         * 多选
         */
        function multiple_select($this) {
            var id =  $this.data('id'),
                label = $this.data('label') || id,
                exist = LA.isset(originalItems, id);

            if ($this.prop('checked')) {
                if (!exist) {
                    originalItems[id] = label;
                }
            } else if (exist) {
                delete originalItems[id];
            }

            if (maxItem > 0 && LA.len(originalItems) > maxItem) {
                unchecked($this);
                delete originalItems[id];
                // 多选项判断最大长度
                return LA.warning(options.exceedMaxItemTip);
            }

            render_tags(originalItems);
        }

        // 单选
        function select($this) {
            var id =  $this.data('id'),
                label = $this.data('label') || id;

            get_all_ckb().each(function () {
                if ($(this).data('id') != id) {
                    unchecked($(this));
                }
            });

            originalItems = {};

            if ($this.prop('checked')) {
                originalItems[id] = label;
            }

            render_tags(originalItems);
        }

        /**
         * 显示选项内容
         *
         * @param items
         */
        function render_tags(items) {
            var ids = [];
            for (var id in items) {
                ids.push(id);
            }

            // 显示勾选的选项内容
            display_input_div(items);
            set_selected_id(ids);

            // 绑定清除事件
            $('.' + clearClass).click(clear_all_tags);
            $('.' + tagClearClass).click(clear_tag);
        }

        function set_selected_id(ids) {
            $hidden.val(ids.length ? ids.join(',') : NONE);
        }

        /**
         * 显示勾选的选项内容
         */
        function display_input_div(tag) {
            if (options.displayer) {
                if (typeof options.displayer == 'string' && LA.isset(self.displayers, options.displayer)) {
                    return self.displayers[options.displayer](tag, $input, options);
                }

                // 自定义选中内容渲染
                return options.displayer(tag, $input, options);
            }

            return self.displayers.default(tag, $input, options);
        }

        function bind_checked_default_event(iframeWin) {
            LA.ready(function () {
                click_checked_items();
                get_all_ckb().change(function () {
                    if (maxItem == 1) {
                        select($(this));
                    } else {
                        multiple_select($(this));
                    }
                });
                if (maxItem == 1) {
                    // 单选模式禁用全选按钮
                    $(layer.getChildFrame('.checkbox-grid .select-all', layerIdx)).click(function () {
                        return false;
                    });
                }
            }, iframeWin);
        }

        function unchecked($ckb) {
            $ckb.parents('tr').css('background-color', 'transparent');
            $ckb.prop('checked', false);
        }

        // 勾选默认选项
        function click_checked_items() {
            setTimeout(function () {
                var ckb = layer.getChildFrame('tbody .checkbox-grid input[type="checkbox"]:checked', layerIdx);
                unchecked(ckb);

                for (var id in originalItems) {
                    layer.getChildFrame('.checkbox-grid input[data-id="'+id+'"]', layerIdx).click();
                }
            }, 10);
        }

        function get_all_ckb() {
            return $(layer.getChildFrame('.checkbox-grid input[type="checkbox"]:not(.select-all)', layerIdx));
        }

        /**
         * 清除所有选项
         */
        function clear_tag() {
            delete originalItems[$(this).data('id')];

            render_tags(originalItems);
        }

        /**
         * 清除所有选项
         */
        function clear_all_tags() {
            originalItems = {};

            render_tags(originalItems);
        }

        function format_area(area) {
            if (w.screen.width <= 750) {
                return ['100%', '100%'];
            }

            return area;
        }

        render_tags(originalItems);
    }

    ResourceSelector.displayers = {
        default: function (tag, $input, opts) {
            var place = '<span class="default-text" style="opacity:0.75">' + (opts.placeholder || $input.attr('placeholder')) + '</span>',
                maxItem = opts.maxItem;
            function _init() {
                if (!LA.len(tag)) {
                    return $input.html(place);
                }
                if (maxItem == 1) {
                    return $input.html(build_one(tag[Object.keys(tag)[0]]));
                }

                $input.html(build_many(tag));
            }


            function build_many(tag) {
                var html = [];

                for (var i in tag) {
                    if (maxItem > 2 || !maxItem) {
                        var strVar = "";
                        strVar += "<li class=\"select2-selection__choice\" >";
                        strVar += tag[i] + " <span data-id=\"" + i + "\" class=\"select2-selection__choice__remove ";
                        strVar += opts.clearOneClass +"\" role=\"presentation\"> ×</span>";
                        strVar += "</li>";

                        html.push(strVar);

                    } else {
                        html.push(
                            "<a class='label label-primary'>" + tag[i] + " " +
                            "<span data-id=" + i + " class='" + opts.clearOneClass +
                            "' style='font-weight:bold;cursor:pointer;font-size:14px'>×</span></a>"
                        )
                    }
                }
                if (!(maxItem > 2 || !maxItem)) {
                    return build_one(html.join('&nbsp;'));
                }

                html.unshift('<span class="select2-selection__clear '+opts.clearAllClass+'">×</span>');

                html = '<ul class="select2-selection__rendered">' + html.join('') + '</ul>';

                return html;

            }

            /**
             * 单个选项样式
             *
             * @param tag
             * @returns {string}
             */
            function build_one(tag) {
                var clearButton = "<div class='pull-right "+opts.clearAllClass+"' style='font-weight:bold;cursor:pointer'>×</div>";

                return ""+tag+""+clearButton;
            }

            _init();
        },

        // list模式
        navList: function (tag, $input, opts) {
            var place = '<span style="opacity:0.75">' + (opts.placeholder || $input.attr('placeholder')) + '</span>',
                maxItem = opts.maxItem;

            function _init() {
                var $app = $(opts.selector).parents('.select-resource').find('app');
                $app.html('');

                if (!LA.len(tag)) {
                    return $input.html(place);
                }
                if (maxItem == 1) {
                    return $input.html(build_one(tag[Object.keys(tag)[0]]));
                }

                $input.html(build_one(opts.selectedOptionsTip.replace(':num', LA.len(tag))));

                $app.html(build_many(tag));
            }

            function build_many(tag) {
                var html = [];

                for (var i in tag) {
                    var strVar = "";
                    strVar += "<li>";
                    strVar += "<a class='pull-left'>" + tag[i] + "</a><a data-id='" + i + "' class='pull-right red ";
                    strVar += opts.clearOneClass +"' ><i class='fa fa-close'></i></a>";
                    strVar += "<span class='clearfix'></span></li>";

                    html.push(strVar);
                }

                html = '<ul class="nav nav-pills nav-stacked" >' + html.join('') + '</ul>';

                return html;

            }

            function build_one(tag) {
                var clearButton = "<div class='pull-right "+opts.clearAllClass+"' style='font-weight:bold;cursor:pointer'>×</div>";

                return ""+tag+""+clearButton;
            }

            _init();
        }
    };

    LA.ResourceSelector = ResourceSelector;

})(window);
