let $ = jQuery,
    pjaxResponded = false,
    bootingCallbacks = [],
    formCallbacks = {
        before: [], success: [], error: []
    };

export default class Dcat {
    constructor(config) {
        this.withConfig(config);
    }

    booting(callback) {
        bootingCallbacks.push(callback)

        return this
    }

    boot() {
        bootingCallbacks.forEach(callback => callback(Vue, router, store));
        bootingCallbacks = []
    }

    liftOff() {
        this.boot()
    }

    ready(callback, _window) {
        if (! _window || _window === window) {
            if (! pjaxResponded) {
                return $(callback);
            }
            return $(document).one('pjax:done', callback);
        }

        var proxy = function (e) {
            _window.$(_window.$('#pjax-container')).one('pjax:done', proxy);

            callback(e);
        };

        _window.Dcat.ready(proxy);
    }

    withConfig(config) {
        this.config = config;
        this.withLang(config['lang']);
        this.withToken(config['token']);

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
}
