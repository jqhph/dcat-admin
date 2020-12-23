(function (w, $) {
    function ExtensionDemo(options) {
        this.options = $.extend({
            $el: $('.demo'),
        }, options);

        this.init(this.options);
    }

    ExtensionDemo.prototype = {
        init: function (options) {
            options.$el.on('click', function () {
                Dcat.success($(this).text());
            });

            console.log('Done.');
        },
    };

    $.fn.extensionDemo = function (options) {
        options = options || {};
        options.$el = $(this);

        return new ExtensionDemo(options);
    };
})(window, jQuery);