
let $d = $(document);

export default class Pjax {
    constructor(Dcat) {
        this.boot(Dcat)
    }

    boot(Dcat) {
        let container = Dcat.config.pjax_container_selector,
            formContainer = 'form[pjax-container]',
            scriptContainer = 'script[data-exec-on-popstate]';

        $.pjax.defaults.timeout = 5000;
        $.pjax.defaults.maxCacheLength = 0;

        $('a:not(a[target="_blank"])').click(function (event) {
            $.pjax.click(event, container, { fragment: 'body' });
        });

        $d.on('pjax:timeout', function (event) {
            event.preventDefault();
        });

        $d.off('submit', formContainer).on('submit', formContainer, function (event) {
            $.pjax.submit(event, container)
        });

        $d.on("pjax:popstate", function () {
            $d.one("pjax:end", function (event) {
                $(event.target).find(scriptContainer).each(function () {
                    $.globalEval(this.text || this.textContent || this.innerHTML || '');
                });
            });
        });

        $d.on('pjax:send', function (xhr) {
            if (xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                $(formContainer).find('[type="submit"],.submit').buttonLoading();
            }
            Dcat.NP.start();
        });

        $d.on('pjax:complete', function (xhr) {
            if (xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                $(formContainer).find('[type="submit"],.submit').buttonLoading(false)
            }

            var $body = $('body');

            // 移除遮罩层
            $(".modal-backdrop").remove();
            $body.removeClass("modal-open");

            // 刷新页面后需要重置modal弹窗设置的间隔
            if ($body.css('padding-right')) {
                $body.css('padding-right', '');
            }
        });

        $d.on('pjax:loaded', () => {
            Dcat.NP.done();
        });
    }
}
