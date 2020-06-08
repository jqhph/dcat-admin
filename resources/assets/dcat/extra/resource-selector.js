(function (w) {
    let NONE = '';

    function ResourceSelector(options) {
        let Dcat = w.Dcat;

        options = $.extend({
            title: '选择', // 弹窗标题
            selector: '', // 选择按钮选择器
            column: '', // 字段名称
            source: '', // 资源地址
            maxItem: 1, // 最大选项数量，0为不限制
            area: ['80%', '90%'],
            queryName: '_resource_',
            items: {}, // 默认选中项，key => value 键值对
            placeholder: '', // input placeholder
            showCloseButton: false,
            lang: {
                close: Dcat.lang.close || '关闭',
                exceed_max_item: Dcat.lang.exceed_max_item || '已超出最大可选择的数量',
                selected_options: Dcat.lang.selected_options || '已选中:num个选项',
            },
            displayerContainer: null, // 选项展示容器dom对象
            hiddenInput: null, // 隐藏表单dom对象
            displayer: null, // 自定义选中项渲染方法
            disabled: false,
            clearAllClass: '',
            clearOneClass: '',
            window: null,
        }, options);

        options.window = options.window || (top || w);

        let self = ResourceSelector,
            column = options.column,
            cls = column.replace(/[\[\]]*/g, '') + (Math.random().toString(36).substr(2)),
            layer = options.window.layer,
            $input = getQueryDomObject(options.displayerContainer) || $(options.selector).parents('.select-resource').find('div[name="' + column + '"]'),
            $hidden = getQueryDomObject(options.hiddenInput) || $input.parents('.input-group').find('input[type="hidden"]'),
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
                clickCheckedItems();
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
                resize: false,
                shade: false,
                scrollbar: false,
                skin: 'select-resource',
                area: formatArea(options.area),
                content: `${options.source}?${options.queryName}=1`,
                btn: options.showCloseButton ? [options.closeButtonText] : null,
                success: function (layero) {
                    iframeWin = options.window[layero.find('iframe')[0]['name']];

                    // 解决页面无法滚动问题
                    $('html').css({'overflow': 'auto'});

                    // 绑定勾选默认选项事件
                    bindCheckedDefaultEvent(iframeWin);
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

        function getQueryDomObject(value) {
            if (! value) {
                return;
            }
            return typeof value === 'object' ? value : $(value);
        }

        /**
         * 多选
         */
        function multipleSelect($this) {
            let id =  $this.data('id'),
                label = $this.data('label') || id,
                exist = Dcat.helpers.isset(originalItems, id);

            if ($this.prop('checked')) {
                if (!exist) {
                    originalItems[id] = label;
                }
            } else if (exist) {
                delete originalItems[id];
            }

            if (maxItem > 0 && Dcat.helpers.len(originalItems) > maxItem) {
                unchecked($this);
                delete originalItems[id];
                // 多选项判断最大长度
                return Dcat.warning(options.lang.exceed_max_item);
            }

            renderTags(originalItems);
        }

        // 单选
        function select($this) {
            let id =  $this.data('id'),
                label = $this.data('label') || id;

            getAllCheckboxes().each(function () {
                if ($(this).data('id') != id) {
                    unchecked($(this));
                }
            });

            originalItems = {};

            if ($this.prop('checked')) {
                originalItems[id] = label;
            }

            renderTags(originalItems);
        }

        /**
         * 显示选项内容
         *
         * @param items
         */
        function renderTags(items) {
            let ids = [];
            for (let id in items) {
                ids.push(id);
            }

            // 显示勾选的选项内容
            displayInputDiv(items);
            setSelectedId(ids);

            // 绑定清除事件
            $('.' + clearClass).click(clearAllTags);
            $('.' + tagClearClass).click(clearTag);
        }

        function setSelectedId(ids) {
            $hidden.val(ids.length ? ids.join(',') : NONE);
        }

        /**
         * 显示勾选的选项内容
         */
        function displayInputDiv(tag) {
            if (options.displayer) {
                if (typeof options.displayer == 'string' && Dcat.helpers.isset(self.displayers, options.displayer)) {
                    return self.displayers[options.displayer](tag, $input, options);
                }

                // 自定义选中内容渲染
                return options.displayer(tag, $input, options);
            }

            return self.displayers.default(tag, $input, options);
        }

        function bindCheckedDefaultEvent(iframeWin) {
            Dcat.ready(function () {
                clickCheckedItems();
                getAllCheckboxes().change(function () {
                    if (maxItem == 1) {
                        select($(this));
                    } else {
                        multipleSelect($(this));
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
            $ckb.parents('tr').css('background-color', '');
            $ckb.prop('checked', false);
        }

        // 勾选默认选项
        function clickCheckedItems() {
            setTimeout(function () {
                let ckb = layer.getChildFrame('tbody .checkbox-grid input[type="checkbox"]:checked', layerIdx);
                unchecked(ckb);

                for (let id in originalItems) {
                    layer.getChildFrame('.checkbox-grid input[data-id="'+id+'"]', layerIdx).click();
                }
            }, 10);
        }

        function getAllCheckboxes() {
            return $(layer.getChildFrame('.checkbox-grid input[type="checkbox"]:not(.select-all)', layerIdx));
        }

        /**
         * 清除所有选项
         */
        function clearTag() {
            delete originalItems[$(this).data('id')];

            renderTags(originalItems);
        }

        /**
         * 清除所有选项
         */
        function clearAllTags() {
            originalItems = {};

            renderTags(originalItems);
        }

        function formatArea(area) {
            if (w.screen.width <= 750) {
                return ['100%', '100%'];
            }

            return area;
        }

        renderTags(originalItems);
    }

    ResourceSelector.displayers = {
        default: function (tag, $input, opts) {
            let place = '<span class="default-text" style="opacity:0.75">' + (opts.placeholder || $input.attr('placeholder')) + '</span>',
                maxItem = opts.maxItem;
            function init() {
                if (! Dcat.helpers.len(tag)) {
                    return $input.html(place);
                }
                if (maxItem == 1) {
                    return $input.html(buildOne(tag[Object.keys(tag)[0]]));
                }

                $input.html(buildMany(tag));
            }


            function buildMany(tag) {
                let html = [];

                for (let i in tag) {
                    if (maxItem > 2 || !maxItem) {
                        let strVar = "";
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
                    return buildOne(html.join('&nbsp;'));
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
            function buildOne(tag) {
                let clearButton = "<div class='pull-right "+opts.clearAllClass+"' style='font-weight:bold;cursor:pointer'>×</div>";

                return ""+tag+""+clearButton;
            }

            init();
        },

        // list模式
        navList: function (tag, $input, opts) {
            let place = '<span style="opacity:0.75">' + (opts.placeholder || $input.attr('placeholder')) + '</span>',
                maxItem = opts.maxItem;

            function init() {
                let $app = $(opts.selector).parents('.select-resource').find('app');
                $app.html('');

                if (! Dcat.helpers.len(tag)) {
                    return $input.html(place);
                }
                if (maxItem == 1) {
                    return $input.html(buildOne(tag[Object.keys(tag)[0]]));
                }

                $input.html(buildOne(opts.lang.selected_options.replace(':num', Dcat.helpers.len(tag))));

                $app.html(buildMany(tag));
            }

            function buildMany(tag) {
                let html = [];

                for (let i in tag) {
                    let strVar = "";
                    strVar += "<li>";
                    strVar += "<a class='pull-left'>" + tag[i] + "</a><a data-id='" + i + "' class='pull-right red text-danger ";
                    strVar += opts.clearOneClass +"' ><i class='fa fa-close'></i></a>";
                    strVar += "<span class='clearfix'></span></li>";

                    html.push(strVar);
                }

                html = '<ul class="nav nav-pills nav-stacked" >' + html.join('') + '</ul>';

                return html;

            }

            function buildOne(tag) {
                let clearButton = "<div class='pull-right "+opts.clearAllClass+"' style='font-weight:bold;cursor:pointer'>×</div>";

                return tag + clearButton;
            }

            init();
        }
    };

    Dcat.ResourceSelector = ResourceSelector;
})(window);
