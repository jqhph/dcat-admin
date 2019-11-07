(function () {
    /**
     * 表单提交
     *
     * @param opts
     * @constructor
     */
    var $eColumns = {};
    LA.Form = function (opts) {
        opts = $.extend({
            $form: null,
            errorClass: 'has-error',
            groupSelector: '.form-group',
            template: '<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> _message_</label><br/>',
            disableRedirect: false, //
            columnSelectors: {}, //
            disableRemoveError: false,
            before: function () {},
            after: function () {},
        }, opts);

        var originalVals = {},
            cls = opts.errorClass,
            groupSlt = opts.groupSelector,
            tpl = opts.template,
            $form = opts.$form,
            tabSelector = '.tab-pane',
            get_tab_id = function ($c) {
                return $c.parents(tabSelector).attr('id');
            },
            get_tab_title_error = function ($c) {
                var id = get_tab_id($c);
                if (!id) return $('<none></none>');
                return $("[href='#" + id + "'] .text-red");
            };

        var self = this;

        // 移除错误信息
        remove_field_error();

        $form.ajaxSubmit({
            beforeSubmit: function (d, f, o) {
                if (opts.before(d, f, o, self) === false) {
                    return false;
                }

                if (fire(LA._form_.before, d, f, o, self) === false) {
                    return false;
                }

                LA.NP.start();
            },
            success: function (d) {
                LA.NP.done();

                if (opts.after(true, d, self) === false) {
                    return;
                }

                if (fire(LA._form_.success, d, self) === false) {
                    return;
                }

                if (!d.status) {
                    LA.error(d.message || 'Save failed!');
                    return;
                }

                LA.success(d.message || 'Save succeeded!');

                if (opts.disableRedirect || d.redirect === false) return;

                if (d.redirect) {
                    return LA.reload(d.redirect);
                }

                history.back(-1);
            },
            error: function (v) {
                LA.NP.done();

                if (opts.after(false, v, self) === false) {
                    return;
                }

                if (fire(LA._form_.error, v, self) === false) {
                    return;
                }

                try {
                    var error = JSON.parse(v.responseText), i;

                    if (v.status != 422 || !error || !LA.isset(error, 'errors')) {
                        return LA.error(v.status + ' ' + v.statusText);
                    }
                    error = error.errors;

                    for (i in error) {
                        // 显示错误信息
                        $eColumns[i] = show_field_error($form, i, error[i]);
                    }

                } catch (e) {
                    return LA.error(v.status + ' ' + v.statusText);
                }
            }
        });

        // 触发钩子事件
        function fire(evs) {
            var i, j, r, args = arguments, p = [];
            delete args[0];
            args = args || [];

            for (j in args) {
                p.push(args[j]);
            }

            for (i in evs) {
                r = evs[i].apply(evs[i], p);

                if (r === false) return r; // 返回 false 会代码阻止继续执行
            }
        }

        // 删除错误有字段的错误信息
        function remove_field_error() {
            var i, p, t;
            for (i in $eColumns) {
                p = $eColumns[i].parents(groupSlt);
                p.removeClass(cls);
                p.find('error').html('');

                t = get_tab_title_error($eColumns[i]);
                if (!t.hasClass('hide')) {
                    t.addClass('hide');
                }

            }
            // 重置
            $eColumns = {};
        }

        // 显示错误信息
        function show_field_error($form, column, errors) {
            var $c = get_field_obj($form, column);

            get_tab_title_error($c).removeClass('hide');

            // 保存字段原始数据
            originalVals[column] = get_val($c);

            if (!$c) {
                if (LA.len(errors) && errors.length) {
                    LA.error(errors.join("  \n  "));
                }
                return;
            }

            var p = $c.closest(groupSlt), j;

            p.addClass(cls);

            for (j in errors) {
                p.find('error').eq(0).append(tpl.replace('_message_', errors[j]));
            }

            if (!opts.disableRemoveError) {
                remove_error_when_val_changed($c, column);
            }

            return $c;
        }

        // 获取字段对象
        function get_field_obj($form, column) {
            if (column.indexOf('.') != -1) {
                column = column.split('.');
                var first = column.shift(), i, sub = '';
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

        // 获取字段值
        function get_val($c) {
            var vals = [],
                t = $c.attr('type'),
                cked = t === 'checkbox' || t === 'radio',
                i;

            for (i = 0; i < $c.length; i++) {
                if (cked) {
                    vals.push($($c[i]).prop('checked'));
                    continue;
                }
                vals.push($($c[i]).val());
            }

            return vals;
        }

        // 当字段值变化时移除错误信息
        function remove_error_when_val_changed($c, column) {
            var p = $c.parents(groupSlt);

            $c.one('change', rm);
            $c.off('blur', rm).on('blur', function () {
                if (val_changed()) rm();
            });

            // 表单值发生变化就移除错误信息
            function autorm() {
                setTimeout(function () {
                    if (!$c.length) return;
                    if (val_changed()) return rm();

                    autorm();
                }, 500);
            }

            autorm();

            // 判断值是否改变
            function val_changed() {
                return !LA.arr.equal(originalVals[column], get_val($c));
            }

            function rm() {
                p.removeClass(cls);
                p.find('error').html('');

                // tab页下没有错误信息了，隐藏title的错误图标
                var id = get_tab_id($c), t;
                if (id && !$('#'+id).find('.'+cls).length) {
                    t = get_tab_title_error($c);
                    if (!t.hasClass('hide')) {
                        t.addClass('hide');
                    }

                }
                delete $eColumns[column];
            }

        }

    };
})();