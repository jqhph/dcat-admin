
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
        });

        $d.on('pjax:loaded', () => {
            Dcat.NP.done();
        });
    }
}
