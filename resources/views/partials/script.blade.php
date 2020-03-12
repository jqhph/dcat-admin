<script>
function Dcat () {}

Dcat.ready = Dcat.pjaxResponded = Dcat.booting = Dcat.beforeSubmit = Dcat.submitted = null;

(function (w, doc) {
    var pjaxResponded = false;

    Dcat.lang = {!! json_encode(__('admin.client') ?: []) !!};

    Dcat._callbacks = {booting:[]};

    Dcat.ready = function (callback, _window) {
        if (! _window || _window === w) {
            if (! pjaxResponded) {
                return $(callback);
            }
            return $(doc).one('pjax:done', callback);
        }

        var $ = _window.$, proxy = function (e) {
            $($('#pjax-container')).one('pjax:done', proxy);

            callback(e);
        };

        _window.Dcat.ready(proxy);
    };

    Dcat.pjaxResponded = function () {
        pjaxResponded = true;
    };

    Dcat._form_ = {
        before: [], success: [], error: []
    };

    {{--注册表单提交前钩子事件--}}
    {{--@param {function} call 返回 false 可以阻止表单提交--}}
    Dcat.beforeSubmit = function (call) {
        typeof call == 'function' && (Dcat._form_.before = [call]);
    };

    {{--@param {function} success 提交成功事件，返回 false 可以阻止默认的表单事件--}}
    {{--@param {function} error 提交出错事件，返回 false 可以阻止默认的表单事件--}}
    Dcat.submitted = function (success, error) {
        typeof success == 'function' && (Dcat._form_.success = [success]);
        typeof error == 'function' && (Dcat._form_.error = [error]);
    };

    {{--
        注册页面初始化事件，相当于：
            $(fn);
            $(document).on('pjax:complete', fn);
    --}}
    Dcat.booting = function (fn) {
        typeof fn == 'function' && (Dcat._callbacks.booting.push(fn));
    };
})(window, document);
</script>