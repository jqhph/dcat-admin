(function (win) {
    function AssetsLoader () {
    }

    AssetsLoader.prototype = {
        // 按顺序加载静态资源
        // 并在所有静态资源加载完毕后执行回调函数
        load: function (urls, callback, args) {
            var self = this;
            if (urls.length < 1) {
                (!callback) || callback(args);
                return;
            }
            seajs.use([urls.pop()], function () {
                self.load(urls, callback, args);
            });
        },
        // 过滤 <script src> 标签
        filterScripts: function (content) {
            var obj = {};

            if (typeof content == 'string') content = $(content);

            obj.scripts = findAll(content, 'script[src]').remove();
            obj.contents = content.not(obj.scripts);

            obj.contents.render = toString;
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
        },

        // 返回过滤 <script src> 标签后的内容，并在加载完 script 脚本后触发 "pjax:script" 事件
        filterScriptAndAutoLoad: function (content, callback) {
            var obj = this.filterScripts(content);

            this.load(obj.js, function () {
                (!callback) || callback(obj.contents);
                fire();
            });

            return obj.contents;
        },
    };

    function findAll(elems, selector) {
        if (typeof elems == 'string') elems = $(elems);
        return elems.filter(selector).add(elems.find(selector));
    }

    function fire () {
        LA.pjaxresponse = 1;
        // js加载完毕 触发 ready 事件
        // setTimeout用于保证在所有js代码最后执行
        setTimeout(LA.triggerReady, 1);
    }

    function toString (th) {
        var html = '', out;
        this.each(function (k, v) {
            if ((out = v.outerHTML)) {
                html += out;
            }
        });
        return html;
    };

    LA.AssetsLoader = new AssetsLoader;
})(window);