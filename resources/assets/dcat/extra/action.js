(function (Dcat) {

    class Action {
        constructor(options) {
            this.options = $.extend({
                selector: null, // 按钮选择器
                event: 'click',
                method: 'POST',
                key: null, // 行主键
                url: null,
                data: {}, // 发送到接口的附加参数
                confirm: null,
                calledClass: null,
                before: function (data, target) {}, // 发起请求之前回调，返回false可以中断请求
                html: function (target, html, data) { // 处理返回的HTML代码
                    target.html(html);
                },
                success: function (target, results) {}, // 请求成功回调，返回false可以中断默认的成功处理逻辑
                error: function (target, results) {}, // 请求出错回调，返回false可以中断默认的错误处理逻辑
            }, options);

            this.init();
        }

        init() {
            let _this = this, options = _this.options;

            $(options.selector).off(options.event).on(options.event, function (e) {
                let data = $(this).data(),
                    target = $(this);
                if (target.attr('loading') > 0) {
                    return;
                }

                if (options.before(data, target, _this) === false) {
                    return;
                }

                // 发起请求
                function request() {
                    target.attr('loading', 1);

                    Object.assign(data, options.data);

                    _this.promise(target, data).then(_this.resolve()).catch(_this.reject());
                }

                var conform = options.confirm;

                if (conform) {
                    Dcat.confirm(conform[0], conform[1], request);
                } else {
                    request()
                }
            });
        }

        resolve() {
            let _this = this, options = _this.options;

            return function (result) {
                var response = result[0],
                    target   = result[1];

                if (options.success(target, response) === false) {
                    return;
                }

                Dcat.handleJsonResponse(response, {html: options.html, target: target});
            };
        }

        reject() {
            let options = this.options;

            return function (result) {
                var request = result[0], target = result[1];

                if (options.success(target, request) === false) {
                    return;
                }

                if (request && typeof request.responseJSON === 'object') {
                    Dcat.error(request.responseJSON.message)
                }
                console.error(result);
            }
        }

        promise(target, data) {
            let options = this.options;

            return new Promise(function (resolve, reject) {
                Object.assign(data, {
                    _action: options.calledClass,
                    _key: options.key,
                    _token: Dcat.token,
                });

                Dcat.NP.start();

                $.ajax({
                    method: options.method,
                    url: options.url,
                    data: data,
                    success: function (data) {
                        target.attr('loading', 0);
                        Dcat.NP.done();
                        resolve([data, target]);
                    },
                    error:function(request){
                        target.attr('loading', 0);
                        Dcat.NP.done();
                        reject([request, target]);
                    }
                });
            });
        }
    }

    Dcat.Action = function (opts) {
        return new Action(opts);
    };
})(Dcat);

