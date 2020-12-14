
let tpl = '<div class="dcat-loading d-flex items-center align-items-center justify-content-center pin" style="{style}">{svg}</div>',
    loading = '.dcat-loading',
    LOADING_SVG = [
        '<svg xmlns="http://www.w3.org/2000/svg" class="mx-auto block" style="width:{width};{svg_style}" viewBox="0 0 120 30" fill="{color}"><circle cx="15" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite"/><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="60" cy="15" r="9" fill-opacity="0.3"><animate attributeName="r" from="9" to="9" begin="0s" dur="0.8s" values="9;15;9" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="0.5" to="0.5" begin="0s" dur="0.8s" values=".5;1;.5" calcMode="linear" repeatCount="indefinite" /></circle><circle cx="105" cy="15" r="15"><animate attributeName="r" from="15" to="15" begin="0s" dur="0.8s" values="15;9;15" calcMode="linear" repeatCount="indefinite" /><animate attributeName="fill-opacity" from="1" to="1" begin="0s" dur="0.8s" values="1;.5;1" calcMode="linear" repeatCount="indefinite" /></circle></svg>',
    ];

class Loading {
    constructor(Dcat, options) {
        options = $.extend({
            container: Dcat.config.pjax_container_selector,
            zIndex: 100,
            width: '52px',
            color: Dcat.color.dark60,
            background: '#fff',
            style: '',
            svg: LOADING_SVG[0]
        }, options);

        let _this = this,
            defStyle = 'position:absolute;',
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
            zIndex: 999991014,
            width: '58px',
            shade: 'rgba(255, 255, 255, 0.1)',
            background: 'transparent',
            top: 200,
            svg: LOADING_SVG[0],
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

    // 给元素附加加载状态
    $.fn.loading = function (opt) {
        if (opt === false) {
            return $(this).find(loading).remove();
        }

        opt = opt || {};
        opt.container = $(this);

        return new Loading(Dcat, opt);
    };

    // 按钮加载状态
    $.fn.buttonLoading = function (start) {
        let $this = $(this),
            loadingId = $this.attr('data-loading'),
            content;

        if (start === false) {
            if (! loadingId) {
                return $this;
            }

            $this.find('.waves-ripple').remove();

            return $this
                .removeClass('disabled btn-loading waves-effect')
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

        let loading = `<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>`;
        let btnClass = ['btn', 'layui-layer-btn0', 'layui-layer-btn1'];

        for (let i in btnClass) {
            if ($this.hasClass(btnClass[i])) {
                loading = LOADING_SVG[0].replace('{color}', 'currentColor').replace('{width}', '50px;height:11px;');
            }
        }

        return $this
            .addClass('disabled btn-loading')
            .attr('disabled', true)
            .attr('data-loading', loadingId)
            .html(`
<div class="${loadingId}" style="display:none">${content}</div>
${loading}
`);
    }

}

export default extend
