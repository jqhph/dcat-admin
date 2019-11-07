(function (w) {
    /**
     * 表单弹窗
     * @param opt
     * @constructor
     */
    LA.ModalForm = function (opt) {
        var number = 1,
            defUrl = opt.defaultUrl,
            btn = opt.buttonSelector,
            area = opt.area,
            title = opt.title,
            lang = {
                submit: opt.lang.submit,
                reset: opt.lang.reset,
                save_failed: opt.lang.save_failed,
            },
            nullFun = function (a, b) {},
            handlers = {
                saved: opt.saved || nullFun,
                success: opt.success || nullFun,
                error: opt.error || nullFun
            },
            lay = w.layer,
            forceRefresh = opt.forceRefresh,
            disableReset = opt.disableReset,
            idx = {},
            $layWin = {},
            queryString = opt.query,
            building,
            submitting,
            $btn;

        (!btn) || $(btn).off('click').click(function () {
            var t = $(this), num = t.attr('number'), url;
            $btn = t;
            if (!num) {
                num = number;
                t.attr('number', number);
                number++;
            }

            url = t.data('url') || defUrl;  // 给弹窗页面链接追加参数
            if (url.indexOf('?') == -1) {
                url += '?'+queryString+'=1'
            } else if (url.indexOf(queryString) == -1) {
                url += '&'+queryString+'=1'
            }
            build(url, num);
        });
        btn || setTimeout(function () {
            build(defUrl, number)
        }, 400);

        // 开始构建弹窗
        function build(url, num) {
            if (!url || building) return;
            if ($layWin[num]) { // 阻止同个类型的弹窗弹出多个
                $layWin[num].show();
                try { lay.restore(idx[num]); } catch (e) {}
                return;
            }
            $(w.document).one('pjax:complete', function () { // 跳转新页面时移除弹窗
                rm(num);
            });

            building = 1;
            (!$btn) || $btn.button('loading');

            $.get(url, function (tpl) {
                building = 0;
                if ($btn) {
                    $btn.button('reset');
                    setTimeout(function () {
                        $btn.find('.waves-ripple').remove();
                    }, 50);
                }
                popup(tpl, num);
            });
        }

        // 弹出弹窗
        function popup(tpl, num) {
            tpl = LA.AssetsLoader.filterScriptAndAutoLoad(tpl).render();
            var t = $(tpl), $form, btns = [lang.submit], opts = {
                type: 1,
                area: formatArea(area),
                content: tpl,
                title: title,
                yes: submit,
                cancel: function () {
                    if (forceRefresh) { // 是否强制刷新
                        $layWin[num] = idx[num] = null;
                    } else {
                        $layWin[num].hide();
                        return false;
                    }
                }
            };

            if (!disableReset) {
                btns.push(lang.reset);

                opts.btn2 = function () { // 重置按钮
                    $form = $form || $('#'+t.find('form').attr('id'));
                    $form.trigger('reset');
                    return false;
                };
            }

            opts.btn = btns;

            idx[num] = lay.open(opts);
            $layWin[num] = w.$('#layui-layer' + idx[num]);

            // 提交表单
            function submit () {
                if (submitting) return;
                $form = $form || w.$('#'+t.find('form').attr('id'));  // 此处必须重新创建jq对象，否则无法操作页面元素

                LA.Form({
                    $form: $form,
                    disableRedirect: true,
                    before: function () {
                        $form.validator('validate');

                        if ($form.find('.has-error').length > 0) {
                            return false;
                        }

                        submitting = 1;

                        $layWin[num].find('.layui-layer-btn0').button('loading');
                    },
                    after: function (success, res) {
                        $layWin[num].find('.layui-layer-btn0').button('reset');
                        submitting = 0;

                        handlers.saved(success, res);

                        if (!success) {
                            return handlers.error(success, res);
                        }
                        if (res.status) {
                            handlers.success(success, res);
                            rm(num);
                            return;
                        }

                        handlers.error(success, res);
                        LA.error(res.message || lang.save_failed);
                    }
                });

            }
        }

        function formatArea(area) {
            if (w.screen.width <= 800) {
                return ['100%', '100%'];
            }

            return area;
        }

        // 移除弹窗
        function rm(num) {
            lay.close(idx[num]);
            $layWin[num] && $layWin[num].remove();
            $layWin[num] = null;
        }
    };
})(top || window);