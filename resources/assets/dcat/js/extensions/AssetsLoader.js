
export default class AssetsLoader {
    constructor(Dcat) {
        let _this = this;

        _this.dcat = Dcat;

        Dcat.assets = {
            // 加载js脚本，并触发 ready 事件
            load: _this.load.bind(_this),

            // 从给定的内容中过滤"<script>"标签内容，并自动加载其中的js脚本
            resolveHtml: _this.resolveHtml.bind(_this)
        };
    }


    // 按顺序加载静态资源
    // 并在所有静态资源加载完毕后执行回调函数
    load(urls, callback, args) {
        let _this = this;
        if (urls.length < 1) {
            (! callback) || callback(args);

            _this.fire();
            return;
        }

        seajs.use([urls.shift()], function () {
            _this.load(urls, callback, args);
        });
    }

    // 过滤 <script src> 标签
    filterScripts(content) {
        var obj = {};

        if (typeof content == 'string') {
            content = $(content);
        }

        obj.scripts = this.findAll(content, 'script[src]').remove();
        obj.contents = content.not(obj.scripts);

        obj.contents.render = this.toString;
        obj.js = (function () {
            var urls = [];
            obj.scripts.each(function (k, v) {
                if (v.src) {
                    urls.push(v.src);
                }
            });

            return urls;
        })();

        return obj;
    }

    // 返回过滤 <script src> 标签后的内容，并在加载完 script 脚本后触发 "pjax:script" 事件
    resolveHtml(content, callback) {
        var obj = this.filterScripts(content);

        this.load(obj.js, function () {
            (!callback) || callback(obj.contents);
        });

        return obj.contents;
    }

    findAll($el, selector) {
        if (typeof $el === 'string') {
            $el = $($el);
        }

        return $el.filter(selector).add($el.find(selector));
    }

    fire() {
        this.dcat.wait();

        // js加载完毕 触发 ready 事件
        // setTimeout用于保证在所有js代码最后执行
        setTimeout(this.dcat.triggerReady, 1);
    }

    toString(th) {
        var html = '', out;

        this.each(function (k, v) {
            if ((out = v.outerHTML)) {
                html += out;
            }
        });

        return html;
    }
}
