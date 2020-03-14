
let $d = $(document);

export default class Pjax {
    constructor(Dcat) {
        this.boot(Dcat)
    }

    boot(Dcat) {
        let container = Dcat.config.pjax_container_selector;

        let _this = this;

        $.pjax.defaults.timeout = 5000;
        $.pjax.defaults.maxCacheLength = 0;

        $('a:not(a[target="_blank"])').click(function (event) {
            $.pjax.click(event, container, { fragment: 'body' });
        });

        $d.on('pjax:timeout', function (event) {
            event.preventDefault();
        });

        $d.off('submit', 'form[pjax-container]').on('submit', 'form[pjax-container]', function (event) {
            $.pjax.submit(event, container)
        });

        $d.on("pjax:popstate", function () {
            $d.one("pjax:end", function (event) {
                $(event.target).find("script[data-exec-on-popstate]").each(function () {
                    $.globalEval(this.text || this.textContent || this.innerHTML || '');
                });
            });
        });

        $d.on('pjax:send', function (xhr) {
            if (xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if ($submit_btn) {
                    $submit_btn.button('loading')
                }
            }
            Dcat.NP.start();
        });

        $d.on('pjax:complete', function (xhr) {
            if (xhr.relatedTarget && xhr.relatedTarget.tagName && xhr.relatedTarget.tagName.toLowerCase() === 'form') {
                var $submit_btn = $('form[pjax-container] :submit');
                if ($submit_btn) {
                    $submit_btn.button('reset')
                }
            }
            Dcat.NP.done();
        });

        // 新页面加载，重新初始化
        $d.on('pjax:loaded', function () {
            _this.boot(Dcat)
        });
    }
}
