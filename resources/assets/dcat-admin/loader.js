(function () {
    var tpl = '<div class="_loading_ flex items-center justify-center pin" style="{style}">{svg}</div>',
        loading = '._loading_',
        LOADING_SVG = [
            '<svg width="{width}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-disk" style="background: none;"><g transform="translate(50,50)"><g ng-attr-transform="scale({{config.scale}})" transform="scale(0.5)"><circle cx="0" cy="0" r="50" ng-attr-fill="{{config.c1}}" fill="{color}"></circle><circle cx="0" ng-attr-cy="{{config.cy}}" ng-attr-r="{{config.r}}" ng-attr-fill="{{config.c2}}" cy="-35" r="15" fill="#ffffff" transform="rotate(101.708)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 0 0;360 0 0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform></circle></g></g></svg>',
            '<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block" style="width:{width};{svg_style}" viewBox="0 0 120 30" fill="{color}"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"/><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle></svg>',
        ];

    /**
     * Loading
     *
     * @param opts
     * @constructor
     */
    function Loader(opts) {
        var defStyle = 'position:absolute;left:10px;right:10px;', content, $container;

        opts = $.extend({
            container: '#pjax-container',
            z_index: 100,
            width: '50px',
            color: '#84bdea',
            bg: '#fff',
            style: '',
            svg: LOADING_SVG[0]
        }, opts);

        $container = opts.container;
        $container = $container == 'object' ? $container : $($container);

        content = $(
            tpl
                .replace('{svg}', opts.svg)
                .replace('{color}', opts.color)
                .replace('{color}', opts.color)
                .replace('{width}', opts.width)
                .replace('{style}', defStyle + 'background:' + opts.bg + ';' + 'z-index:' + opts.z_index + ';' + opts.style)
        );
        content.appendTo($container);

        this.remove = function () {
            $container.find(loading).remove();
        };
    }

    Loader.destroyAll = function () {
        $(loading).remove();
    };

    LA.Loader = Loader;

    // 全屏居中loading
    LA.loading = function (opts) {
        if (opts === false) {
            // 关闭loading
            return setTimeout(LA.Loader.destroyAll, 70);
        }
        // 配置参数
        opts = $.extend({
            color: '#62abe4',
            z_index: 999991014,
            width: '58px',
            shade: 'rgba(255, 255, 255, 0.02)',
            top: 200,
            svg: LOADING_SVG[1],
        }, opts);

        var win = $(window),
            // 容器
            $container = $('<div class="_loading_" type="loading" times="1" showtime="0" contype="string" style="z-index:'+opts.z_index+';width:300px;position:fixed"></div>'),
            // 遮罩层直接沿用layer
            shadow = $('<div class="layui-layer-shade _loading_" style="z-index:'+(opts.z_index-2)+'; background-color:'+opts.shade+'"></div>');
        $container.appendTo('body');
        if (opts.shade) {
            shadow.appendTo('body');
        }

        function resize() {
            $container.css({
                left: (win.width() - 300)/2,
                top: (win.height() - opts.top)/2
            });
        }
        // 自适应窗口大小
        win.on('resize', resize);
        resize();

        $container.loading(opts);
    };

    $.fn.loading = function (opt) {
        if (opt === false) {
            return $(this).find(loading).remove();
        }

        opt = opt || {};
        opt.container = $(this);

        return new Loader(opt);
    };
})();