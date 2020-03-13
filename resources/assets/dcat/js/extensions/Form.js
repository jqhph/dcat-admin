
import '../jquery-form/jquery.form.min';

let $eColumns = {},
    formCallbacks = {
        before: [], success: [], error: []
    };

class Form {
    constructor(options) {
        let _this = this;

        _this.options = $.extend({
            // 表单的 jquery 对象或者css选择器
            form: null,
            // 表单错误信息class
            errorClass: 'has-error',
            // 表单组css选择器
            groupSelector: '.form-group',
            // tab表单css选择器
            tabSelector: '.tab-pane',
            // 错误信息模板
            errorTemplate: '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> {message}</label><br/>',
            // 保存成功后不允许跳转
            disableRedirect: false,
            // 不允许自动移除表单错误信息
            disableAutoRemoveError: false,
            // 表单提交之前事件监听，返回false可以中止表单继续提交
            before: function () {},
            // 表单提交之后事件监听，返回false可以中止后续逻辑
            after: function () {},
        }, options);

        _this.originalValues = {};
        _this.$form = typeof _this.options.form === 'object'
            ? _this.options.form
            : $(_this.options.form).first();
    }

    _execute() {
        let _this = this,
            $form = _this.$form,
            options = _this.options;

        // 移除错误信息
        removeFieldError(_this);

        $form.ajaxSubmit({
            beforeSubmit: function (fields, $form, options) {
                if (options.before(fields, $form, options, _this) === false) {
                    return false;
                }

                if (fire(formCallbacks.before, fields, $form, options, _this) === false) {
                    return false;
                }

                Dcat.NP.start();
            },
            success: function (response) {
                Dcat.NP.done();

                if (options.after(true, response, _this) === false) {
                    return;
                }

                if (fire(formCallbacks.success, response, _this) === false) {
                    return;
                }

                if (! response.status) {
                    Dcat.error(response.message || 'Save failed!');
                    return;
                }

                Dcat.success(response.message || 'Save succeeded!');

                if (options.disableRedirect || response.redirect === false) {
                    return;
                }

                if (response.redirect) {
                    return Dcat.reload(response.redirect);
                }

                history.back(-1);
            },
            error: function (response) {
                Dcat.NP.done();

                if (options.after(false, response, _this) === false) {
                    return;
                }

                if (fire(formCallbacks.error, response, _this) === false) {
                    return;
                }

                try {
                    var error = JSON.parse(response.responseText), i;

                    if (response.status != 422 || ! error || ! Dcat.helpers.isset(error, 'errors')) {
                        return Dcat.error(response.status + ' ' + response.statusText);
                    }
                    error = error.errors;

                    for (i in error) {
                        // 显示错误信息
                        $eColumns[i] = _this.showFieldError($form, i, error[i]);
                    }

                } catch (e) {
                    return Dcat.error(response.status + ' ' + response.statusText);
                }
            }
        });
    }

    // 显示错误信息
    showFieldError($form, column, errors) {
        let _this = this,
            $field = _this.queryFieldByName($form, column);

        queryTabTitleError(_this, $field).removeClass('hide');

        // 保存字段原始数据
        _this.originalValues[column] = _this.getFieldValue($field);

        if (! $field) {
            if (Dcat.helpers.len(errors) && errors.length) {
                Dcat.error(errors.join("  \n  "));
            }
            return;
        }

        let $group = $field.closest(_this.options.groupSelector), j;

        $group.addClass(_this.options.errorClass);

        for (j in errors) {
            $group.find('error').eq(0).append(
                _this.options.errorTemplate.replace('{message}', errors[j])
            );
        }

        if (! _this.options.disableAutoRemoveError) {
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

    removeError($field) {
        let parent = $field.parents(this.options.groupSelector),
            errorClass = this.options.errorClass;

        parent.removeClass(errorClass);
        parent.find('error').html('');

        // tab页下没有错误信息了，隐藏title的错误图标
        let tab;
        
        if (! queryTabByField(this, $field).find('.'+errorClass).length) {
            tab = queryTabTitleError(this, $field);
            if (! tab.hasClass('hide')) {
                tab.addClass('hide');
            }
        }

        delete $eColumns[column];
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
    let _this = form,
        removeError = function () {
            _this.removeError($field)
        };

    $field.one('change', removeError);
    $field.off('blur', removeError).on('blur', function () {
        if (_this.isValueChanged($field, column))  {
            removeError();
        }
    });

    // 表单值发生变化就移除错误信息
    function handle() {
        setTimeout(function () {
            if (! $field.length) {
                return;
            }
            if (_this.isValueChanged($field, column)) {
                return removeError();
            }

            handle();
        }, 500);
    }

    handle();
}

// 删除错误有字段的错误信息
function removeFieldError(form) {
    let i, parent, tab;

    for (i in $eColumns) {
        parent = $eColumns[i].parents(form.options.groupSelector);
        parent.removeClass(form.options.errorClass);
        parent.find('error').html('');

        tab = queryTabTitleError($eColumns[i]);
        if (! tab.hasClass('hide')) {
            tab.addClass('hide');
        }

    }
    // 重置
    $eColumns = {};
}

function getTabId(form, $field) {
    return $field.parents(form.options.tabSelector).attr('id');
}

function queryTabByField(form, $field)
{
    let id = getTabId(form, $field);

    if (! id) {
        return $('<none></none>');
    }

    return $('#' + id);
}

function queryTabTitleError(form, $field) {
    return queryTabByField(form, $field).find('.text-red');
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

export default Form
