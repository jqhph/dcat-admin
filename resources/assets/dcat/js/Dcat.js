let $ = jQuery,
    pjaxResponded = false,
    bootingCallbacks = [],
    formCallbacks = {
        before: [], success: [], error: []
    },
    defaultOptions = {
        pjax_container_selector: '#pjax-container',
    };

export default class Dcat {
    constructor(config) {
        this.withConfig(config);
    }

    booting(callback) {
        bootingCallbacks.push(callback);

        return this
    }

    boot() {
        bootingCallbacks.forEach(callback => callback(this));
        bootingCallbacks = []
    }

    ready(callback, _window) {
        if (! _window || _window === window) {
            if (! pjaxResponded) {
                return $(callback);
            }
            return $(document).one('pjax:loaded', callback);
        }

        var proxy = function (e) {
            _window.$(_window.$(this.pjaxContainer)).one('pjax:loaded', proxy);

            callback(e);
        };

        _window.Dcat.ready(proxy);
    }

    withConfig(config) {
        this.config = $.extend(defaultOptions, config);
        this.withLang(config.lang);
        this.withToken(config.token);

        delete config.lang;
        delete config.token;

        return this
    }

    withToken(token) {
        token && (this.token = token);

        return this
    }

    withLang(lang) {
        lang && (this.lang = lang);

        return this
    }

    pjaxResponded() {
        pjaxResponded = true;

        return this
    }

    submiting(callback) {
        typeof callback == 'function' && (formCallbacks.before.push(callback));

        return this
    }

    submitted(success, error) {
        typeof success == 'function' && (formCallbacks.success.push(success));
        typeof error == 'function' && (formCallbacks.error.push(error));

        return this
    }

    reload(url) {
        let container = this.config.pjax_container_selector;
        let opt = {container: container};

        url && (opt.url = url);

        $.pjax.reload(opt);
    }
}
