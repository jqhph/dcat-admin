<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;

trait HasRemoteData
{
    /**
     * @var string
     */
    protected $_url;

    /**
     * @var array
     */
    protected $_selectors = [];

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
    public function request(string $url, array $query = [])
    {
        $this->_url = admin_url(Helper::urlWithQuery($url, $query));

        return $this;
    }

    /**
     * 请求当前地址.
     *
     * @param string $url
     *
     * @return $this
     */
    public function requestCurrent(array $query = [])
    {
        return $this->request(request()->fullUrlWithQuery($query));
    }

    /**
     * 获取请求地址
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
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
     * @param string $script
     *
     * @return $this
     */
    public function fetching(string $script)
    {
        $this->_scripts['fetching'][] = $script;

        return $this;
    }

    /**
     * 设置抓取完数据后执行的js代码.
     *
     * @param string $script
     *
     * @return $this
     */
    public function fetched(string $script)
    {
        $this->_scripts['fetched'][] = $script;

        return $this;
    }

    /**
     * @return bool
     */
    public function allowBuildRequestScript()
    {
        return $this->_url === null ? false : true;
    }

    /**
     * @return bool|string
     */
    public function buildRequestScript()
    {
        if (! $this->allowBuildRequestScript()) {
            return false;
        }

        $fetching = implode(';', $this->_scripts['fetching']);
        $fetched = implode(';', $this->_scripts['fetched']);

        $binding = '';
        foreach ($this->_selectors as $v) {
            $binding .= "$('{$v}').click(function () { request($(this).data()) });";
        }

        return <<<JS
(function () {
    var f;
    function request(p) {
        if (f) return;
        f = 1;
        
        {$fetching};     
        $.ajax({
          url: '{$this->_url}',
          dataType: 'json',
          data: $.extend({_token:Dcat.token}, p || {}),
          success: function (response) {
            f = 0;
            {$fetched};
          },
          error: function (a, b, c) {
              f = 0;
              Dcat.ajaxError(a, b, c)
          },
        });
    }
    request();
    $binding
})();
JS;
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
        $this->_url = $fetcher->getUrl();

        $this->_selectors = $fetcher->getButtonSelectors();

        $scripts = $fetcher->getRequestScripts();

        $this->_scripts['fetching'] = array_merge($this->_scripts['fetching'], $scripts['fetching']);
        $this->_scripts['fetched'] = array_merge($this->_scripts['fetched'], $scripts['fetched']);

        return $this;
    }
}
