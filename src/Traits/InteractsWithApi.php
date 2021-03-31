<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;

/**
 * Trait InteractsWithApi.
 *
 *
 * @method mixed handle(Request $request)
 * @method mixed valueResult()
 */
trait InteractsWithApi
{
    use HasAuthorization;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * @var string
     */
    protected $uriKey;

    /**
     * @var array
     */
    protected $requestSelectors = [];

    /**
     * @var array
     */
    protected $requestScripts = [
        'fetching' => [],
        'fetched'  => [],
    ];

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * 返回请求附带参数.
     *
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * 设置请求地址.
     *
     * @param string $method
     * @param string $url
     * @param array $query
     *
     * @return $this
     */
    public function request(string $method, string $url, array $query = [])
    {
        $this->method = $method;
        $this->url = admin_url(Helper::urlWithQuery($url, $query));

        return $this;
    }

    /**
     * 获取请求地址
     *
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->url ?: route(admin_api_route_name('value'));
    }

    /**
     * 获取请求方法.
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->method;
    }

    /**
     * 设置URI标识.
     *
     * @return string
     */
    public function getUriKey()
    {
        return $this->uriKey ?: static::class;
    }

    /**
     * 获取js代码.
     *
     * @return array
     */
    public function getRequestScripts()
    {
        return $this->requestScripts;
    }

    /**
     * 设置点击抓取数据的按钮的css选择器.
     *
     * @param string|array $selector
     *
     * @return $this
     */
    public function click($selector)
    {
        $this->requestSelectors =
            array_merge($this->requestSelectors, (array) $selector);

        return $this;
    }

    /**
     * @return array
     */
    public function getRequestSelectors()
    {
        return $this->requestSelectors;
    }

    /**
     * 设置抓取数据时执行的js代码.
     *
     * @param string|\Closure $script
     *
     * @return $this
     */
    public function fetching($script)
    {
        $this->requestScripts['fetching'][] = value($script);

        return $this;
    }

    /**
     * 设置抓取完数据后执行的js代码.
     *
     * @param string|\Closure $script
     *
     * @return $this
     */
    public function fetched($script)
    {
        $this->requestScripts['fetched'][] = value($script);

        return $this;
    }

    /**
     * 判断是否使用请求接口功能.
     *
     * @return bool
     */
    public function allowBuildRequest()
    {
        return (
            $this->url
            || method_exists($this, 'handle')
        ) ? true : false;
    }

    /**
     * 构建请求数据js代码.
     *
     * @return null|string
     */
    public function buildRequestScript()
    {
        if (! $this->allowBuildRequest()) {
            return;
        }

        $fetching = implode(';', $this->requestScripts['fetching']);
        $fetched = implode(';', $this->requestScripts['fetched']);

        return <<<JS
(function () {
    var loading;
    function request(data) {
        if (loading) {
            return;
        }
        loading = 1;

        data = $.extend({$this->formatRequestData()}, data || {});

        {$fetching};

        $.ajax({
          url: '{$this->getRequestUrl()}',
          dataType: 'json',
          method: '{$this->method}',
          data: data,
          success: function (response) {
            loading = 0;
            {$fetched};
          },
          error: function (a, b, c) {
              loading = 0;
              Dcat.handleAjaxError(a, b, c)
          },
        });
    }

    request();

    {$this->buildBindingScript()}
})();
JS;
    }

    /**
     * @return string
     */
    private function formatRequestData()
    {
        $data = [
            '_key'   => $this->getUriKey(),
            '_token' => csrf_token(),
        ];

        return json_encode(
            array_merge($this->parameters(), $data)
        );
    }

    /**
     * @return string
     */
    private function buildBindingScript()
    {
        $script = '';

        foreach ($this->requestSelectors as $v) {
            $script .= <<<JS
$('{$v}').on('click', function () {
    request($(this).data())
});
JS;
        }

        return $script;
    }

    /**
     * 合并.
     *
     * @param static $self
     *
     * @return $this
     */
    public function merge($self)
    {
        $this->url = $self->getRequestUrl();
        $this->method = $self->getRequestMethod();
        $this->uriKey = $self->getUriKey();
        $this->requestSelectors = $self->getRequestSelectors();
        $this->parameters = array_merge($this->parameters, $self->parameters());

        $scripts = $self->getRequestScripts();

        $this->requestScripts['fetching'] = array_merge($this->requestScripts['fetching'], $scripts['fetching']);
        $this->requestScripts['fetched'] = array_merge($this->requestScripts['fetched'], $scripts['fetched']);

        return $this;
    }
}
