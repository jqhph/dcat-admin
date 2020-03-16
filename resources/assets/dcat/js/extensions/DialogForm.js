
let w = top || window;

export default class DialogForm {
    constructor(Dcat, options) {
        let _this = this, nullFun = function (a, b) {};

        _this.options = $.extend({
            // 弹窗标题
            title: '',
            // 默认地址
            defaultUrl: '',
            // 需要绑定的按钮选择器
            buttonSelector: '',
            // 弹窗大小
            area: [],
            // 语言包
            lang: {
                submit: Dcat.lang['submit'] || 'Submit',
                reset: Dcat.lang['reset'] || 'Reset',
            },

            // get参数名称
            query: '',

            // 保存成功后是否刷新页面
            forceRefresh: false,
            disableReset: false,

            // 执行保存操作后回调
            saved: nullFun,
            // 保存成功回调
            success: nullFun,
            // 保存失败回调
            error: nullFun,
        }, options);

        _this.$form = null;
        _this._dialog = w.layer;
        _this._counter = 1;
        _this._idx = {};
        _this._dialogs = {};
        _this.isLoading = 0;
        _this.isSubmitting = 0;

        _this._execute(options)
    }

    _execute(options) {
        let _this = this,
            defUrl = options.defaultUrl,
            $btn;

        (! options.buttonSelector) || $(options.buttonSelector).off('click').click(function () {
            $btn = $(this);

            let num = $btn.attr('counter'), url;

            if (! num) {
                num = _this._counter;

                $btn.attr('counter', num);

                _this._counter++;
            }

            url = $btn.data('url') || defUrl;  // 给弹窗页面链接追加参数

            if (url.indexOf('?') === -1) {
                url += '?' + options.query + '=1'
            } else if (url.indexOf(options.query) === -1) {
                url += '&' + options.query + '=1'
            }

            _this._build($btn, url, num);
        });

        options.buttonSelector || setTimeout(function () {
            _this._build($btn, defUrl, _this._counter)
        }, 400);
    }

    _build($btn, url, counter) {
        let _this = this;

        if (! url || _this.isLoading) {
            return;
        }

        if (_this._dialogs[counter]) { // 阻止同个类型的弹窗弹出多个
            _this._dialogs[counter].show();

            try {
                _this._dialog.restore(_this._idx[counter]);
            } catch (e) {
            }

            return;
        }

        $(w.document).one('pjax:complete', function () { // 跳转新页面时移除弹窗
            _this._destory(counter);
        });

        _this.isLoading = 1;

        (! $btn) || $btn.button('loading');

        $.get(url, function (tpl) {
            _this.isLoading = 0;

            if ($btn) {
                $btn.button('reset');

                setTimeout(function () {
                    $btn.find('.waves-ripple').remove();
                }, 50);
            }

            _this._popup(tpl, counter);
        });
    }

    _popup(tpl, counter) {
        let _this = this,
            options = _this.options;

        tpl = Dcat.assets.filterScriptsAndLoad(tpl).render();
        
        let $template = $(tpl),
            btns = [options.lang.submit],
            dialogOpts = {
                type: 1,
                area: (function (v) {
                        if (w.screen.width <= 800) {
                            return ['100%', '100%',];
                        }
    
                        return v;
                    })(options.area),
                content: tpl,
                title: title,
                yes: function () {
                    _this._submit($template)
                },
                cancel: function () {
                    if (options.forceRefresh) { // 是否强制刷新
                        _this._dialogs[counter] = _this._idx[counter] = null;
                    } else {
                        _this._dialogs[counter].hide();
                        return false;
                    }
                }
            };

        if (! options.disableReset) {
            btns.push(options.lang.reset);

            dialogOpts.btn2 = function () { // 重置按钮
                _this.$form = _this.$form || $template.find('form').first();

                _this.$form.trigger('reset');
                
                return false;
            };
        }

        dialogOpts.btn = btns;

        _this._idx[counter] = _this._dialog.open(dialogOpts);
        _this._dialogs[counter] = w.$('#layui-layer' + _this._idx[counter]);
    }

    _destory(counter) {
        let dialogs = this._dialogs;

        this._dialog.close(this._idx[counter]);

        dialogs[counter] && dialogs[counter].remove();

        dialogs[counter] = null;
    }

    _submit($template) {
        let _this = this, options = _this.options;

        if (_this.isSubmitting) {
            return;
        }

        _this.$form = _this.$form || $template.find('form').first();  // 此处必须重新创建jq对象，否则无法操作页面元素

        Dcat.Form({
            form: _this.$form,
            disableRedirect: true,
            before: function () {
                _this.$form.validator('validate');

                if (_this.$form.find('.has-error').length > 0) {
                    return false;
                }

                _this.isSubmitting = 1;

                _this._dialogs[num].find('.layui-layer-btn0').button('loading');
            },
            after: function (success, res) {
                _this._dialogs[num].find('.layui-layer-btn0').button('reset');

                _this.isSubmitting = 0;

                options.saved(success, res);

                if (!success) {
                    return options.error(success, res);
                }
                if (res.status) {
                    options.success(success, res);

                    _this._destory(num);

                    return;
                }

                options.error(success, res);

                Dcat.error(res.message || 'Save failed.');
            }
        });

        return false;
    }
}
