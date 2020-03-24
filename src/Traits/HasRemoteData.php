<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;

/**
 * Trait HasRemoteData
 *
 * @package Dcat\Admin\Traits
 *
 * @method mixed handle(Request $request)
 * @method array requestData()
 * @method mixed result()
 */
trait HasRemoteData
{
    use HasAuthorization;

    /**
     * @var string
     */
    protected $_url;

    /**
     * @var array
     */
    protected $_selectors = [];

    /**
     * @var string 
     */
    protected $_method = 'POST';

    /**
     * @var array
     */
    protected $_scripts = [
        'fetching' => [],
        'fetched'  => [],
    ];

    /**
     * 设置请求地址.
     *
     * @param string $url
     *
     * @return $this
     */
    public function request(string $url, array $query = [], string $method = 'GET')
    {
        $this->_url = admin_url(Helper::urlWithQuery($url, $query));

        $this->_method = $method;

        return $this;
    }

    /**
     * 请求当前地址.
     *
     * @param string $url
     *
     * @return $this
     */
    public function requestCurrent(array $query = [], string $method = 'GET')
    {
        return $this->request(request()->fullUrlWithQuery($query), [], $method);
    }

    /**
     * 获取请求地址
     *
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->_url ?: route('dcat.api.value');
    }

    /**
     * 获取请求方法
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->_method;
    }

    /**
     * 设置URI标识.
     *
     * @return string
     */
    public function requestUriKey()
    {
        return static::class;
    }

    /**
     * 获取js代码.
     *
     * @return array
     */
    public function getRequestScripts()
    {
        return $this->_scripts;
    }

    /**
     * 设置点击抓取数据的按钮的css选择器.
     *
     * @param string|array $selector
     *
     * @return $this
     */
    public function refetch($selector)
    {
        $this->_selectors =
            array_merge($this->_selectors, (array) $selector);

        return $this;
    }

    /**
     * @return array
     */
    public function getButtonSelectors()
    {
        return $this->_selectors;
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
        $this->_scripts['fetching'][] = value($script);

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
        $this->_scripts['fetched'][] = value($script);

        return $this;
    }

    /**
     * 判断是否允许构建js代码
     *
     * @return bool
     */
    public function allowBuildRequestScript()
    {
        return $this->_url === null ? false : true;
    }

    /**
     * 构建请求数据js代码.
     *
     * @return null|string
     */
    public function buildRequestScript()
    {
        if (! $this->allowBuildRequestScript()) {
            return null;
        }

        $fetching = implode(';', $this->_scripts['fetching']);
        $fetched = implode(';', $this->_scripts['fetched']);

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
          method: '{$this->_method}',
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
            '_key' => $this->requestUriKey(),
        ];

        if (method_exists($this, 'requestData')) {
            $data = array_merge($data, $this->requestData());
        }

        return json_encode($data);
    }

    /**
     * @return string
     */
    private function buildBindingScript()
    {
        $script = '';

        foreach ($this->_selectors as $v) {
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
     * @param static $fetcher
     *
     * @return $this
     */
    public function merge($fetcher)
    {
        $this->_url = $fetcher->getRequestUrl();
        $this->_method = $fetcher->getRequestMethod();

        $this->_selectors = $fetcher->getButtonSelectors();

        $scripts = $fetcher->getRequestScripts();

        $this->_scripts['fetching'] = array_merge($this->_scripts['fetching'], $scripts['fetching']);
        $this->_scripts['fetched'] = array_merge($this->_scripts['fetched'], $scripts['fetched']);

        return $this;
    }
}
