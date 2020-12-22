
import Helpers from './extensions/Helpers'
import Translator from './extensions/Translator'

let $ = jQuery,
    pjaxResponded = false,
    bootingCallbacks = [],
    actions = {},
    defaultOptions = {
        pjax_container_selector: '#pjax-container',
    };

export default class Dcat {
    constructor(config) {
        this.token = null;
        this.lang = null;

        // 工具函数
        new Helpers(this);

        this.withConfig(config);
    }

    /**
     * 初始化事件监听方法
     *
     * @param callback
     * @param once
     * @returns {Dcat}
     */
    booting(callback, once) {
        once = once === undefined ? true : once;

        bootingCallbacks.push([callback, once]);

        return this
    }

    /**
     * 初始化事件监听方法，每个请求都会触发
     *
     * @param callback
     * @returns {Dcat}
     */
    bootingEveryRequest(callback) {
        return this.booting(callback, false)
    }

    /**
     * 初始化
     */
    boot() {
        let _this = this,
            callbacks = bootingCallbacks;

        bootingCallbacks = [];

        callbacks.forEach(data => {
            data[0](this);

            if (data[1] === false) {
                bootingCallbacks.push(data)
            }
        });

        // 脚本加载完毕后重新触发
        _this.onPjaxLoaded(_this.boot.bind(this))
    }

    /**
     * 监听所有js脚本加载完毕事件，需要用此方法代替 $.ready 方法
     * 此方法允许在iframe中监听父窗口的事件
     *
     * @param callback
     * @param _window
     * @returns {*|jQuery|*|jQuery.fn.init|jQuery|HTMLElement}
     */
    ready(callback, _window) {
        let _this = this;

        if (! _window || _window === window) {
            if (! pjaxResponded) {
                return $(callback);
            }

            return _this.onPjaxLoaded(callback);
        }

        function run(e) {
            _window.$(_this.config.pjax_container_selector).one('pjax:loaded', run);

            callback(e);
        }

        _window.Dcat.ready(run);
    }

    /**
     * 主动触发 ready 事件
     */
    triggerReady() {
        if (! pjaxResponded) {
            return;
        }

        $(() => {
            $(document).trigger('pjax:loaded');
        });
    }

    /**
     * 如果是 pjax 响应的页面，需要调用此方法
     *
     * @returns {Dcat}
     */
    pjaxResponded(value) {
        pjaxResponded = value !== false;

        return this
    }

    /**
     * 使用pjax重载页面
     *
     * @param url
     */
    reload(url) {
        let container = this.config.pjax_container_selector,
            opt = {container: container};

        if ($(container).length) {
            url && (opt.url = url);

            $.pjax.reload(opt);

            return;
        }

        if (url) {
            location.href = url;
        } else {
            location.reload();
        }
    }

    /**
     * 监听pjax加载js脚本完毕事件方法，此事件在 pjax:complete 事件之后触发
     *
     * @param callback
     * @param once 默认true
     *
     * @returns {*|jQuery}
     */
    onPjaxLoaded(callback, once) {
        once = once === undefined ? true : once;

        if (once) {
            return $(document).one('pjax:loaded', callback);
        }

        return $(document).on('pjax:loaded', callback);
    }

    /**
     * 监听pjax加载完毕完毕事件方法
     *
     * @param callback
     * @param once 默认true
     * @returns {*|jQuery}
     */
    onPjaxComplete(callback, once) {
        once = once === undefined ? true : once;

        if (once) {
            return $(document).one('pjax:complete', callback);
        }

        return $(document).on('pjax:complete', callback);
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
        if (lang && typeof lang === 'object') {
            this.lang = this.Translator(lang);
        }

        return this
    }

    // 语言包
    Translator(lang) {
        return new Translator(this, lang);
    }

    // 注册动作
    addAction(name, callback) {
        if (typeof callback === 'function') {
            actions[name] = callback;
        }
    }

    // 获取动作
    actions() {
        return actions
    }
}
