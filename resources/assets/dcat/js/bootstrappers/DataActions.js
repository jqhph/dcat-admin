import Dropdown from "../../../adminlte/js/Dropdown";

let $document = $(document);

let defaultActions = {
    // 刷新按钮
    refresh: function (action, Dcat) {
        $document.on('click', action, function () {
            Dcat.reload($(this).data('url'));
        });
    },
    // 删除按钮初始化
    delete: function (action, Dcat) {
        let lang = Dcat.lang;

        $document.on('click', action, function() {
            let url = $(this).data('url'),
                redirect = $(this).data('redirect'),
                msg = $(this).data('message');

            Dcat.confirm(lang.delete_confirm, msg, function () {
                Dcat.NP.start();
                $.delete({
                    url: url,
                    success: function (response) {
                        Dcat.NP.done();

                        response.data.detail = msg;

                        Dcat.handleJsonResponse(response);
                    }
                });
            });
        });
    },
    // 批量删除按钮初始化
    'batch-delete': function (action, Dcat) {
        $document.on('click', action, function() {
            let url = $(this).data('url'),
                name = $(this).data('name'),
                keys = Dcat.grid.selected(name),
                lang = Dcat.lang;

            if (! keys.length) {
                return;
            }
            let msg = 'ID - ' + keys.join(', ');

            Dcat.confirm(lang.delete_confirm, msg, function () {
                Dcat.NP.start();
                $.delete({
                    url: url + '/' + keys.join(','),
                    success: function (data) {
                        Dcat.NP.done();

                        Dcat.handleJsonResponse(data);
                    }
                });
            });
        });
    },

    // 图片预览
    'preview-img': function (action, Dcat) {
        $document.on('click', action, function () {
            return Dcat.helpers.previewImage($(this).attr('src'));
        });
    },

    'popover': function (action, Dcat) {
        Dcat.onPjaxComplete(function () {
            $('.popover').remove();
        }, false);

        $document.on('click', action, function () {
            $(this).popover()
        });
    },

    'box-actions': function () {
        $document.on('click', '.box [data-action="collapse"]', function (e) {
            e.preventDefault();

            $(this).find('i').toggleClass('icon-minus icon-plus');

            $(this).closest('.box').find('.box-body').first().collapse("toggle");
        });

        // Close box
        $document.on('click', '.box [data-action="remove"]', function () {
            $(this).closest(".box").removeClass().slideUp("fast");
        });
    },

    dropdown: function () {
        function hide() {
            $('.dropdown-menu').removeClass('show')
        }
        $document.on('click', hide);

        function toggle(event) {
            var $this = $(this);

            $('.dropdown-menu').each(function () {
                if ($this.next()[0] !== this) {
                    $(this).removeClass('show');
                }
            });

            $this.Dropdown('toggleSubmenu')
        }

        function fix(event) {
            event.preventDefault()
            event.stopPropagation()

            setTimeout(function() {
                $(this).Dropdown('fixPosition')
            }, 1)
        }

        let selector = '[data-toggle="dropdown"]';

        $document
            .on('click', selector, toggle)
            .on('click', selector, fix);
    }
};

export default class DataActions {
    constructor(Dcat) {
        let actions = $.extend(defaultActions, Dcat.actions()),
            name;

        for (name in actions) {
            actions[name](`[data-action="${name}"]`, Dcat);
        }
    }
}
