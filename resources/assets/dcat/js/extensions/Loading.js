
let tpl = '<div class="dcat-loading d-flex items-center align-items-center justify-content-center pin" style="{style}">{svg}</div>',
    loading = '.dcat-loading',
    LOADING_SVG = [
        '<svg width="{width}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-disk" style="background: none;"><g transform="translate(50,50)"><g ng-attr-transform="scale({{config.scale}})" transform="scale(0.5)"><circle cx="0" cy="0" r="50" ng-attr-fill="{{config.c1}}" fill="{color}"></circle><circle cx="0" ng-attr-cy="{{config.cy}}" ng-attr-r="{{config.r}}" ng-attr-fill="{{config.c2}}" cy="-35" r="15" fill="#ffffff" transform="rotate(101.708)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 0 0;360 0 0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform></circle></g></g></svg>',
        '<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block" style="width:{width};{svg_style}" viewBox="0 0 120 30" fill="{color}"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"/><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle></svg>',
    ];

class Loading {
    constructor(Dcat, options) {
        options = $.extend({
            container: Dcat.config.pjax_container_selector,
            zIndex: 100,
            width: '52px',
            color: '#7985d0',
            background: '#fff',
            style: '',
            svg: LOADING_SVG[0]
        }, options);

        let _this = this,
            defStyle = 'position:absolute;left:10px;right:10px;',
            content;

        _this.$container = $(options.container);

        content = $(
            tpl
                .replace('{svg}', options.svg)
                .replace('{color}', options.color)
                .replace('{color}', options.color)
                .replace('{width}', options.width)
                .replace('{style}', `${defStyle}background:${options.background};z-index:${options.zIndex};${options.style}`)
        );
        content.appendTo(_this.$container);
    }

    destroy() {
        this.$container.find(loading).remove();
    }
}

function destroyAll() {
    $(loading).remove();
}

function extend(Dcat) {
    // 全屏居中loading
    Dcat.loading = function (options) {
        if (options === false) {
            // 关闭loading
            return setTimeout(destroyAll, 70);
        }
        // 配置参数
        options = $.extend({
            color: '#5c6bc6',
            zIndex: 999991014,
            width: '58px',
            shade: 'rgba(255, 255, 255, 0.1)',
            background: 'transparent',
            top: 200,
            svg: LOADING_SVG[1],
        }, options);

        var win = $(window),
            // 容器
            $container = $('<div class="dcat-loading" style="z-index:'+options.zIndex+';width:300px;position:fixed"></div>'),
            // 遮罩层直接沿用layer
            shadow = $('<div class="layui-layer-shade dcat-loading" style="z-index:'+(options.zIndex-2)+'; background-color:'+options.shade+'"></div>');

        $container.appendTo('body');

        if (options.shade) {
            shadow.appendTo('body');
        }

        function resize() {
            $container.css({
                left: (win.width() - 300)/2,
                top: (win.height() - options.top)/2
            });
        }
        // 自适应窗口大小
        win.on('resize', resize);
        resize();

        $container.loading(options);
    };

    //
    $.fn.loading = function (opt) {
        if (opt === false) {
            return $(this).find(loading).remove();
        }

        opt = opt || {};
        opt.container = $(this);

        return new Loading(Dcat, opt);
    };

    $.fn.buttonLoading = function (start) {
        let $this = $(this),
            loadingId = $this.data('loading'),
            content;

        if (start === false) {
            if (! loadingId) {
                return $this;
            }
            return $this
                .removeClass('disabled btn-loading')
                .removeAttr('disabled')
                .removeAttr('data-loading')
                .html(
                    $this.find('.' + loadingId).html()
                );
        }

        if (loadingId) {
            return $this;
        }

        content = $this.html();

        loadingId = 'ld-'+Dcat.helpers.random();

        return $this
            .addClass('disabled btn-loading')
            .attr('disabled', true)
            .attr('data-loading', loadingId)
            .html(`
<div class="${loadingId}" style="display:none">${content}</div>
<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
 LOADING ...
`);
    }

}

export default extend
