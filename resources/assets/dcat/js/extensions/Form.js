
import '../jquery-form/jquery.form.min';

let formCallbacks = {
        before: [], success: [], error: []
    };

class Form {
    constructor(options) {
        let _this = this;

        _this.options = $.extend({
            // 表单的 jquery 对象或者css选择器
            form: null,
            // 开启表单验证
            validate: false,
            // 确认弹窗
            confirm: {title: null, content: null},
            // 是否使用Toastr展示字段验证错误信息
            validationErrorToastr: false,
            // 表单错误信息class
            errorClass: 'has-error',
            // 表单错误信息容器选择器
            errorContainerSelector: '.with-errors',
            // 表单组css选择器
            groupSelector: '.form-group,.form-label-group,.form-field',
            // tab表单css选择器
            tabSelector: '.tab-pane',
            // 错误信息模板
            errorTemplate: '<label class="control-label" for="inputError"><i class="feather icon-x-circle"></i> {message}</label><br/>',
            // 是否允许跳转
            redirect: true,
            // 自动移除表单错误信息
            autoRemoveError: true,
            // 表单提交之前事件监听，返回false可以中止表单继续提交
            before: function () {},
            // 表单提交之后事件监听，返回false可以中止后续逻辑
            after: function () {},
            // 成功事件，返回false可以中止后续逻辑
            success: function () {},
            // 失败事件，返回false可以中止后续逻辑
            error: function () {},
        }, options);

        _this.originalValues = {};
        _this.$form = $(_this.options.form).first();
        _this._errColumns = {};

        _this.init();
    }

    init() {
        let _this = this;
        let confirm = _this.options.confirm;

        if (! confirm.title) {
            return _this.submit();
        }

        Dcat.confirm(confirm.title, confirm.content, function () {
            _this.submit();
        });
    }

    submit() {
        let _this = this,
            $form = _this.$form,
            options = _this.options,
            $submitButton = $form.find('[type="submit"],.submit');

        // 移除所有错误信息
        _this.removeErrors();

        $form.ajaxSubmit({
            data: {_token: Dcat.token},
            beforeSubmit: function (fields, form, _opt) {
                if (options.before(fields, form, _opt, _this) === false) {
                    return false;
                }

                // 触发全局事件
                if (fire(formCallbacks.before, fields, form, _opt, _this) === false) {
                    return false;
                }

                // 开启表单验证
                if (options.validate) {
                    $form.validator('validate');

                    if ($form.find('.' + options.errorClass).length > 0) {
                        return false;
                    }
                }

                $submitButton.buttonLoading();
            },
            success: function (response) {
                setTimeout(function () {
                    $submitButton.buttonLoading(false);
                }, 700);

                if (options.after(true, response, _this) === false) {
                    return;
                }

                if (options.success(response, _this) === false) {
                    return;
                }

                if (fire(formCallbacks.success, response, _this) === false) {
                    return;
                }

                if (response.redirect === false || ! options.redirect) {
                    if (response.data && response.data.then) {
                        delete response.data['then'];
                        delete response.data['then'];
                        delete response.data['then'];
                    }
                }

                Dcat.handleJsonResponse(response);
            },
            error: function (response) {
                $submitButton.buttonLoading(false);

                if (options.after(false, response, _this) === false) {
                    return;
                }

                if (options.error(response, _this) === false) {
                    return;
                }

                if (fire(formCallbacks.error, response, _this) === false) {
                    return;
                }

                try {
                    var error = JSON.parse(response.responseText),
                        key;

                    if (response.status != 422 || ! error || ! Dcat.helpers.isset(error, 'errors')) {
                        return Dcat.error(response.status + ' ' + response.statusText);
                    }
                    error = error.errors;

                    for (key in error) {
                        // 显示错误信息
                        _this._errColumns[key] = _this.showError($form, key, error[key]);
                    }

                } catch (e) {
                    return Dcat.error(response.status + ' ' + response.statusText);
                }
            }
        });
    }

    // 显示错误信息
    showError($form, column, errors) {
        let _this = this,
            $field = _this.queryFieldByName($form, column),
            $group = $field.closest(_this.options.groupSelector),
            render = function (msg) {
                $group.addClass(_this.options.errorClass);

                if (typeof msg === 'string') {
                    msg = [msg];
                }

                for (let j in msg) {
                    $group.find(_this.options.errorContainerSelector).first().append(
                        _this.options.errorTemplate.replace('{message}', msg[j])
                    );
                }

                if (_this.options.validationErrorToastr) {
                    Dcat.error(msg.join('<br/>'));
                }
            };

        queryTabTitleError(_this, $field).removeClass('d-none');

        // 保存字段原始数据
        _this.originalValues[column] = _this.getFieldValue($field);

        if (! $field) {
            if (Dcat.helpers.len(errors) && errors.length) {
                Dcat.error(errors.join("  \n  "));
            }
            return;
        }

        render(errors);

        if (_this.options.autoRemoveError) {
            removeErrorWhenValChanged(_this, $field, column);
        }

        return $field;
    }

    // 获取字段值
    getFieldValue($field) {
        let vals = [],
            type = $field.attr('type'),
            checker = type === 'checkbox' || type === 'radio',
            i;

        for (i = 0; i < $field.length; i++) {
            if (checker) {
                vals.push($($field[i]).prop('checked'));
                continue;
            }

            vals.push($($field[i]).val());
        }

        return vals;
    }

    // 判断值是否改变
    isValueChanged($field, column) {
        return ! Dcat.helpers.equal(this.originalValues[column], this.getFieldValue($field));
    }

    // 获取字段jq对象
    queryFieldByName($form, column) {
        if (column.indexOf('.') !== -1) {
            column = column.split('.');

            let first = column.shift(),
                i,
                sub = '';

            for (i in column) {
                sub += '[' + column[i] + ']';
            }
            column = first + sub;
        }

        var $c = $form.find('[name="' + column + '"]');

        if (!$c.length) $c = $form.find('[name="' + column + '[]"]');

        if (!$c.length) {
            $c = $form.find('[name="' + column.replace(/start$/, '') + '"]');
        }
        if (!$c.length) {
            $c = $form.find('[name="' + column.replace(/end$/, '') + '"]');
        }

        if (!$c.length) {
            $c = $form.find('[name="' + column.replace(/start\]$/, ']') + '"]');
        }
        if (!$c.length) {
            $c = $form.find('[name="' + column.replace(/end\]$/, ']') + '"]');
        }

        return $c;
    }

    // 移除给定字段的错误信息
    removeError($field, column) {
        let options = this.options,
            parent = $field.parents(options.groupSelector),
            errorClass = this.errorClass;

        parent.removeClass(errorClass);
        parent.find(options.errorContainerSelector).html('');

        // tab页下没有错误信息了，隐藏title的错误图标
        let tab;

        if (! queryTabByField(this, $field).find('.'+errorClass).length) {
            tab = queryTabTitleError(this, $field);
            if (! tab.hasClass('d-none')) {
                tab.addClass('d-none');
            }
        }

        delete this._errColumns[column];
    }

    // 删除所有错误信息
    removeErrors() {
        let _this = this,
            column,
            tab;

        // 移除所有字段的错误信息
        _this.$form.find(_this.options.errorContainerSelector).each(function (_, $err) {
            $($err).parents(_this.options.groupSelector).removeClass(_this.options.errorClass);
            $($err).html('');
        });

        // 移除tab表单tab标题错误信息
        for (column in _this._errColumns) {
            tab = queryTabTitleError(_this._errColumns[column]);
            if (! tab.hasClass('d-none')) {
                tab.addClass('d-none');
            }
        }

        // 重置
        _this._errColumns = {};
    }
}

// 监听表单提交事件
Form.submitting = function (callback) {
    typeof callback == 'function' && (formCallbacks.before.push(callback));

    return this
};

// 监听表单提交完毕事件
Form.submitted = function (success, error) {
    typeof success == 'function' && (formCallbacks.success.push(success));
    typeof error == 'function' && (formCallbacks.error.push(error));

    return this
};

// 当字段值变化时移除错误信息
function removeErrorWhenValChanged(form, $field, column) {
    let remove = function () {
        form.removeError($field, column)
    };

    $field.one('change', remove);
    $field.off('blur', remove).on('blur', function () {
        if (form.isValueChanged($field, column))  {
            remove();
        }
    });

    // 表单值发生变化就移除错误信息
    let interval = function () {
        setTimeout(function () {
            if (! $field.length) {
                return;
            }
            if (form.isValueChanged($field, column)) {
                return remove();
            }

            interval();
        }, 500);
    };

    interval();
}


function getTabId(form, $field) {
    return $field.parents(form.options.tabSelector).attr('id');
}

function queryTabByField(form, $field)
{
    let tabId = getTabId(form, $field);

    if (! tabId) {
        return $('<none></none>');
    }

    return $(`a[href="#${tabId}"]`);
}

function queryTabTitleError(form, $field) {
    return queryTabByField(form, $field).find('.has-tab-error');
}

// 触发钩子事件
function fire(callbacks) {
    let i, j,
        result,
        args = arguments,
        argsArr = [];

    delete args[0];

    args = args || [];

    for (j in args) {
        argsArr.push(args[j]);
    }

    for (i in callbacks) {
        result = callbacks[i].apply(callbacks[i], argsArr);

        if (result === false) {
            return result; // 返回 false 会代码阻止继续执行
        }
    }
}


// 开启form表单模式
$.fn.form = function (options) {
    let $this = $(this);

    options = $.extend(options, {
        form: $this,
    });

    $this.on('submit', function () {
        return false;
    });

    $this.find('[type="submit"],.submit').click(function (e) {
        Dcat.Form(options);

        return false;
    });
};

export default Form
