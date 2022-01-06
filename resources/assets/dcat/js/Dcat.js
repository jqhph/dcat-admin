
import Helpers from './extensions/Helpers'
import Translator from './extensions/Translator'

let $ = jQuery,
    $document = $(document),
    waiting = false,
    bootingCallbacks = [],
    actions = {},
    initialized = {},
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
            if (! waiting) {
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
     * 监听动态生成元素.
     *
     * @param selector
     * @param callback
     * @param options
     */
    init(selector, callback, options) {
        let self = this,
            clear = function () {
                if (initialized[selector]) {
                    initialized[selector].disconnect();
                }
            };

        $document.one('pjax:complete', clear);

        clear();

        setTimeout(function () {
            initialized[selector] = $.initialize(selector, function () {
                let $this = $(this),
                    id = $this.attr('id');

                if ($this.attr('initialized')) {
                    return;
                }
                $this.attr('initialized', '1');

                // 如果没有ID，则自动生成
                if (! id) {
                    id = "_"+self.helpers.random();
                    $this.attr('id', id);
                }

                callback.call(this, $this, id)
            }, options);
        });
    }

    /**
     * 清理注册过的init回调.
     *
     * @param selector
     */
    offInit(selector) {
        if (initialized[selector]) {
            initialized[selector].disconnect();
        }

        $(document).trigger('dcat:init:off', selector, initialized[selector])

        initialized[selector] = null;
    }

    /**
     * 主动触发 ready 事件
     */
    triggerReady() {
        if (! waiting) {
            return;
        }

        $(() => {
            $document.trigger('pjax:loaded');
        });
    }

    /**
     * 等待JS脚本加载完成
     *
     * @returns {Dcat}
     */
    wait(value) {
        waiting = value !== false;

        $document.trigger('dcat:waiting');

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
            return $document.one('pjax:loaded', callback);
        }

        return $document.on('pjax:loaded', callback);
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
            return $document.one('pjax:complete', callback);
        }

        return $document.on('pjax:complete', callback);
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
