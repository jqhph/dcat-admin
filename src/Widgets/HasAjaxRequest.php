<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Support\Str;

trait HasAjaxRequest
{
    /**
     * @var string
     */
    protected $__url;

    /**
     * @var array
     */
    protected $__selectors = [];

    /**
     * @var array
     */
    protected $__scripts = [
        'fetching' => [],
        'fetched'  => [],
    ];

    /**
     * Set request url.
     *
     * @param string $url
     *
     * @return $this
     */
    public function request(string $url)
    {
        return $this->setUrl($url);
    }

    /**
     * Set current url to request.
     *
     * @param string $url
     *
     * @return $this
     */
    public function requestCurrent(array $query = [])
    {
        $this->__url = url(request()->getPathInfo()).'?'.http_build_query($query);

        return $this;
    }

    /**
     * Set request url.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->__url = admin_url($url);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->__url;
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return $this->__scripts;
    }

    /**
     * @param string|array $selector
     *
     * @return $this
     */
    public function refetch($selector)
    {
        $this->__selectors =
            array_merge($this->__selectors, (array) $selector);

        return $this;
    }

    /**
     * @return array
     */
    public function getButtonSelectors()
    {
        return $this->__selectors;
    }

    /**
     * Set the script before fetch data.
     *
     * @param string $script
     *
     * @return $this
     */
    public function fetching(string $script)
    {
        $this->__scripts['fetching'][] = $script;

        return $this;
    }

    /**
     * Set the script after fetch data.
     *
     * @param string $script
     *
     * @return $this
     */
    public function fetched(string $script)
    {
        $this->__scripts['fetched'][] = $script;

        return $this;
    }

    /**
     * @return bool
     */
    public function allowBuildRequestScript()
    {
        return $this->__url === null ? false : true;
    }

    /**
     * @return string
     */
    public function generateScriptFunctionName()
    {
        return 'ajax_request_'.Str::random(8);
    }

    /**
     * @return bool|string
     */
    public function buildRequestScript()
    {
        if (! $this->allowBuildRequestScript()) {
            return false;
        }

        $fetching = implode(';', $this->__scripts['fetching']);
        $fetched = implode(';', $this->__scripts['fetched']);

        $binding = '';
        foreach ($this->__selectors as $v) {
            $binding .= "$('{$v}').click(function () { request($(this).data()) });";
        }

        return <<<JS
(function () {
    var f;
    function request(p) {
        if (f) return;
        f = 1;
        
        $fetching;     
        $.ajax({
          url: '{$this->__url}',
          dataType: 'json',
          data: $.extend({_token:LA.token}, p || {}),
          success: function (response) {
            f = 0;
            {$fetched};
          },
          error: function (a, b, c) {
              f = 0;
              LA.ajaxError(a, b, c)
          },
        });
    }
    request();
    $binding
})();
JS;
    }

    /**
     * Copy the given AjaxRequestBuilder.
     *
     * @param HasAjaxRequest $fetcher
     *
     * @return $this
     */
    public function copy($fetcher)
    {
        $this->__url = $fetcher->getUrl();

        $this->__selectors = $fetcher->getButtonSelectors();

        $scripts = $fetcher->getScripts();

        $this->__scripts['fetching'] = array_merge($this->__scripts['fetching'], $scripts['fetching']);
        $this->__scripts['fetched'] = array_merge($this->__scripts['fetched'], $scripts['fetched']);

        return $this;
    }
}
