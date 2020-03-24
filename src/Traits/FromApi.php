<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;

/**
 * Trait FromApi
 *
 * @package Dcat\Admin\Traits
 *
 * @method mixed handle(Request $request)
 * @method mixed result()
 */
trait FromApi
{
    use HasAuthorization;

    /**
     * @var string
     */
    protected $fromUrl;

    /**
     * @var string
     */
    protected $fromMethod = 'POST';

    /**
     * @var string
     */
    protected $fromUriKey;

    /**
     * @var array
     */
    protected $fromSelectors = [];

    /**
     * @var array
     */
    protected $fromScripts = [
        'fetching' => [],
        'fetched'  => [],
    ];

    /**
     * 获取请求附带参数.
     *
     * @return array
     */
    public function parameters(): array
    {
        return [];
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
    public function from(string $method, string $url, array $query = [])
    {
        $this->fromMethod = $method;
        $this->fromUrl = admin_url(Helper::urlWithQuery($url, $query));

        return $this;
    }

    /**
     * 请求当前地址.
     *
     * @param array $query
     * @param string $method
     *
     * @return $this
     */
    public function fromCurrent(array $query = [], string $method = 'GET')
    {
        return $this->from($method, request()->fullUrlWithQuery($query));
    }

    /**
     * 获取请求地址
     *
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->fromUrl ?: route('dcat.api.value');
    }

    /**
     * 获取请求方法
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->fromMethod;
    }

    /**
     * 设置URI标识.
     *
     * @return string
     */
    public function getFromUriKey()
    {
        return $this->fromUriKey ?: static::class;
    }

    /**
     * 获取js代码.
     *
     * @return array
     */
    public function getRequestScripts()
    {
        return $this->fromScripts;
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
        $this->fromSelectors =
            array_merge($this->fromSelectors, (array) $selector);

        return $this;
    }

    /**
     * @return array
     */
    public function getFromSelectors()
    {
        return $this->fromSelectors;
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
        $this->fromScripts['fetching'][] = value($script);

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
        $this->fromScripts['fetched'][] = value($script);

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
            $this->fromUrl
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
            return null;
        }

        $fetching = implode(';', $this->fromScripts['fetching']);
        $fetched = implode(';', $this->fromScripts['fetched']);

        return <<<JS
(function () {
    var requesting;
    function request(data) {
        if (requesting) {
            return;
        }
        requesting = 1;
        
        data = $.extend({$this->formatRequestData()}, data || {});
        
        {$fetching};   
        
        $.ajax({
          url: '{$this->getRequestUrl()}',
          dataType: 'json',
          method: '{$this->fromMethod}',
          data: $.extend({_token: Dcat.token}, data),
          success: function (response) {
            requesting = 0;
            {$fetched};
          },
          error: function (a, b, c) {
              requesting = 0;
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
            '_key' => $this->getFromUriKey(),
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

        foreach ($this->fromSelectors as $v) {
            $script .= <<<JS
$('{$v}').click(function () { 
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
        $this->fromUrl = $self->getRequestUrl();
        $this->fromMethod = $self->getRequestMethod();
        $this->fromUriKey = $self->getFromUriKey();
        $this->fromSelectors = $self->getFromSelectors();

        $scripts = $self->getRequestScripts();

        $this->fromScripts['fetching'] = array_merge($this->fromScripts['fetching'], $scripts['fetching']);
        $this->fromScripts['fetched'] = array_merge($this->fromScripts['fetched'], $scripts['fetched']);

        return $this;
    }
}
