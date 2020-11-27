
let w = window;

if (top && w.layer) {
    w = top;
}

export default class DialogForm {
    constructor(Dcat, options) {
        let self = this, nullFun = function () {};

        self.options = $.extend({
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
            resetButton: true,

            // 执行保存操作后回调
            saved: nullFun,
            // 保存成功回调
            success: nullFun,
            // 保存失败回调
            error: nullFun,
        }, options);

        // 表单
        self.$form = null;
        // 目标按钮
        self.$target = null;
        self._dialog = w.layer;
        self._counter = 1;
        self._idx = {};
        self._dialogs = {};
        self.rendering = 0;
        self.submitting = 0;

        self.init(options)
    }

    init(options) {
        let self = this,
            defUrl = options.defaultUrl,
            selector = options.buttonSelector;

        selector && $(selector).off('click').click(function () {
            self.$target = $(this);

            let counter = self.$target.attr('counter'), url;

            if (! counter) {
                counter = self._counter;

                self.$target.attr('counter', counter);

                self._counter++;
            }

            url = self.$target.data('url') || defUrl;  // 给弹窗页面链接追加参数

            if (url.indexOf('?') === -1) {
                url += '?' + options.query + '=1'
            } else if (url.indexOf(options.query) === -1) {
                url += '&' + options.query + '=1'
            }

            self._build(url, counter);
        });

        selector || setTimeout(function () {
            self._build(defUrl, self._counter)
        }, 400);
    }

    // 构建表单
    _build(url, counter) {
        let self = this,
            $btn = self.$target;

        if (! url || self.rendering) {
            return;
        }

        if (self._dialogs[counter]) { // 阻止同个类型的弹窗弹出多个
            self._dialogs[counter].show();

            try {
                self._dialog.restore(self._idx[counter]);
            } catch (e) {
            }

            return;
        }

        // 刷新或跳转页面时移除弹窗
        Dcat.onPjaxComplete(() => {
            self._destroy(counter);
        });

        self.rendering = 1;

        $btn && $btn.buttonLoading();

        Dcat.NP.start();

        // 请求表单内容
        $.ajax({
            url: url,
            success: function (template) {
                self.rendering = 0;
                Dcat.NP.done();

                if ($btn) {
                    $btn.buttonLoading(false);

                    setTimeout(function () {
                        $btn.find('.waves-ripple').remove();
                    }, 50);
                }

                self._popup(template, counter);
            }
        });
    }

    // 弹出弹窗
    _popup(template, counter) {
        let self = this,
            options = self.options;

        // 加载js代码
        template = Dcat.assets.resolveHtml(template).render();
        
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
                    self.submit()
                },
                cancel: function () {
                    if (options.forceRefresh) { // 是否强制刷新
                        self._dialogs[counter] = self._idx[counter] = null;
                    } else {
                        self._dialogs[counter].hide();
                        return false;
                    }
                }
            };

        if (options.resetButton) {
            btns.push(options.lang.reset);

            dialogOpts.btn2 = function () { // 重置按钮
                self.$form.trigger('reset');
                
                return false;
            };
        }

        dialogOpts.btn = btns;

        self._idx[counter] = self._dialog.open(dialogOpts);
        self._dialogs[counter] = w.$('#layui-layer' + self._idx[counter]);
        self.$form = self._dialogs[counter].find('form').first();
    }

    // 销毁弹窗
    _destroy(counter) {
        let dialogs = this._dialogs;

        this._dialog.close(this._idx[counter]);

        dialogs[counter] && dialogs[counter].remove();

        dialogs[counter] = null;
    }

    // 提交表单
    submit() {
        let self = this, 
            options = self.options,
            counter = self.$target.attr('counter'),
            $submitBtn = self._dialogs[counter].find('.layui-layer-btn0');

        if (self.submitting) {
            return;
        }

        Dcat.Form({
            form: self.$form,
            redirect: false,
            confirm: Dcat.FormConfirm,
            before: function () {
                // 验证表单
                self.$form.validator('validate');

                if (self.$form.find('.has-error').length > 0) {
                    return false;
                }

                self.submitting = 1;

                $submitBtn.buttonLoading();
            },
            after: function (status, response) {
                $submitBtn.buttonLoading(false);

                self.submitting = 0;

                if (options.saved(status, response) === false) {
                    return false;
                }

                if (! status) {
                    return options.error(status, response);
                }
                if (response.status) {
                    let r = options.success(status, response);

                    self._destroy(counter);

                    return r;
                }

                return options.error(status, response);
            }
        });

        return false;
    }
}
