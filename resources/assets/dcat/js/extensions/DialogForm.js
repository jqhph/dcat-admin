
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

        // 表单
        _this.$form = null;
        // 目标按钮
        _this.$target = null;
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
            defUrl = options.defaultUrl;

        (! options.buttonSelector) || $(options.buttonSelector).off('click').click(function () {
            _this.$target = $(this);

            let counter = _this.$target.attr('counter'), url;

            if (! counter) {
                counter = _this._counter;

                _this.$target.attr('counter', counter);

                _this._counter++;
            }

            url = _this.$target.data('url') || defUrl;  // 给弹窗页面链接追加参数

            if (url.indexOf('?') === -1) {
                url += '?' + options.query + '=1'
            } else if (url.indexOf(options.query) === -1) {
                url += '&' + options.query + '=1'
            }

            _this._build(url, counter);
        });

        options.buttonSelector || setTimeout(function () {
            _this._build(defUrl, _this._counter)
        }, 400);
    }

    // 构建表单
    _build(url, counter) {
        let _this = this,
            $btn = _this.$target;

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

        // 刷新或跳转页面时移除弹窗
        Dcat.onPjaxComplete(() => {
            _this._destroy(counter);
        });

        _this.isLoading = 1;

        $btn && $btn.buttonLoading();

        Dcat.NP.start();

        // 请求表单内容
        $.get(url, function (template) {
            _this.isLoading = 0;
            Dcat.NP.done();

            if ($btn) {
                $btn.buttonLoading(false);

                setTimeout(function () {
                    $btn.find('.waves-ripple').remove();
                }, 50);
            }

            _this._popup(template, counter);
        });
    }

    // 弹出弹窗
    _popup(template, counter) {
        let _this = this,
            options = _this.options;

        // 加载js代码
        template = Dcat.assets.filterScriptsAndLoad(template).render();
        
        let btns = [options.lang.submit],
            dialogOpts = {
                type: 1,
                area: (function (v) {
                        // 屏幕小于800则最大化展示
                        if (w.screen.width <= 800) {
                            return ['100%', '100%',];
                        }
    
                        return v;
                    })(options.area),
                content: template,
                title: options.title,
                yes: function () {
                    _this._submit()
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
                _this.$form.trigger('reset');
                
                return false;
            };
        }

        dialogOpts.btn = btns;

        _this._idx[counter] = _this._dialog.open(dialogOpts);
        _this._dialogs[counter] = w.$('#layui-layer' + _this._idx[counter]);
        _this.$form = _this._dialogs[counter].find('form').first();
    }

    // 销毁弹窗
    _destroy(counter) {
        let dialogs = this._dialogs;

        this._dialog.close(this._idx[counter]);

        dialogs[counter] && dialogs[counter].remove();

        dialogs[counter] = null;
    }

    // 提交表单
    _submit() {
        let _this = this, 
            options = _this.options,
            counter = _this.$target.attr('counter'),
            $submitBtn = _this._dialogs[counter].find('.layui-layer-btn0');

        if (_this.isSubmitting) {
            return;
        }

        Dcat.Form({
            form: _this.$form,
            disableRedirect: true,
            before: function () {
                // 验证表单
                _this.$form.validator('validate');

                if (_this.$form.find('.has-error').length > 0) {
                    return false;
                }

                _this.isSubmitting = 1;

                $submitBtn.buttonLoading();
            },
            after: function (success, res) {
                $submitBtn.buttonLoading(false);

                _this.isSubmitting = 0;

                options.saved(success, res);

                if (!success) {
                    return options.error(success, res);
                }
                if (res.status) {
                    options.success(success, res);

                    _this._destroy(counter);

                    return;
                }

                options.error(success, res);

                Dcat.error(res.message || 'Save failed.');
            }
        });

        return false;
    }
}
