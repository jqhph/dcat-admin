<script>
    function LA() {}

    LA.lang = {!! json_encode(trans('admin.client') ?: []) !!};

    LA.components = {booting:[]};

    LA.ready = function (callback, win) {
        if (!win || win === window) {
            if (typeof LA.pjaxresponse == 'undefined') {
                return $(callback, win || window);
            }
            return $(document, win || window).one('pjax:script', callback);
        }

        var proxy = function (e) {
            win.$(win.$('#pjax-container')).one('pjax:script', proxy);

            callback(e);
        };

        win.LA.ready(proxy);
    };

    LA._form_ = {
        before: [], success: [], error: []
    };

    {{--注册表单提交前钩子事件--}}
    {{--@param {function} call 返回 false 可以阻止表单提交--}}
    LA.beforeSubmit = function (call) {
        typeof call == 'function' && (LA._form_.before = [call]);
    };

    {{--@param {function} success 提交成功事件，返回 false 可以阻止默认的表单事件--}}
    {{--@param {function} error 提交出错事件，返回 false 可以阻止默认的表单事件--}}
    LA.submitted = function (success, error) {
        typeof success == 'function' && (LA._form_.success = [success]);
        typeof error == 'function' && (LA._form_.error = [error]);
    };

    {{--
        注册页面初始化事件，相当于：
            $(fn);
            $(document).on('pjax:complete', fn);
    --}}
    LA.booting = function (fn) {
        typeof fn == 'function' && (LA.components.booting.push(fn));
    };


</script>