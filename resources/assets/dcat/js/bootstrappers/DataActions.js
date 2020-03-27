
let defaultActions = {
    // 刷新按钮
    refresh: function ($action, Dcat) {
        return function () {
            Dcat.reload($(this).data('url'));
        };
    },
    // 删除按钮初始化
    delete: function ($action, Dcat) {
        let lang = Dcat.lang;

        return function() {
            let url = $(this).data('url'),
                redirect = $(this).data('redirect');

            Dcat.confirm(lang.delete_confirm, url, function () {
                Dcat.NP.start();
                $.ajax({
                    method: 'post',
                    url: url,
                    data: {
                        _method: 'delete',
                        _token: Dcat.token,
                    },
                    success: function (data) {
                        Dcat.NP.done();
                        if (data.status) {
                            Dcat.reload(redirect);
                            Dcat.swal.success(data.message, url);
                        } else {
                            Dcat.swal.error(data.message, url);
                        }
                    }
                });
            });
        };
    },
    // 批量删除按钮初始化
    'batch-delete': function ($action, Dcat) {
        return function() {
            let url = $(this).data('url'),
                name = $(this).data('name'),
                keys = Dcat.grid.selected(name),
                lang = Dcat.lang;

            if (! keys.length) {
                return;
            }
            Dcat.confirm(lang.delete_confirm, keys.join(', '), function () {
                Dcat.NP.start();
                $.ajax({
                    method: 'post',
                    url: url + '/' + keys.join(','),
                    data: {
                        _method: 'delete',
                        _token: Dcat.token,
                    },
                    success: function (data) {
                        Dcat.NP.done();
                        if (data.status) {
                            Dcat.reload();
                            Dcat.swal.success(data.message, keys.join(', '));
                        } else {
                            Dcat.swal.error(data.message, keys.join(', '));
                        }
                    }
                });
            });
        };
    },

    // 图片预览
    'preview-img': function ($action, Dcat) {
        return function () {
            return Dcat.helpers.previewImage($(this).attr('src'));
        };
    },

    'popover': function ($action) {
        $('.popover').remove();

        return function () {
            $action.popover()
        };
    },

    'box-actions': function () {
        $('.box [data-action="collapse"]').click(function (e) {
            e.preventDefault();

            $(this).find('i').toggleClass('icon-minus icon-plus');

            $(this).closest('.box').find('.box-body').first().collapse("toggle");
        });

        // Close box
        $('.box [data-action="remove"]').click(function () {
            $(this).closest(".box").removeClass().slideUp("fast");
        });
    }
};

export default class DataActions {
    constructor(Dcat) {
        let actions = $.extend(defaultActions, Dcat.actions()),
            $action,
            name,
            func;

        for (name in actions) {
            $action = $(`[data-action="${name}"]`);

            func = actions[name]($action, Dcat);

            if (typeof func === 'function') {
                // 必须先取消再绑定，否则可能造成重复绑定的效果
                $action.off('click').click(func);
            }
        }
    }
}
