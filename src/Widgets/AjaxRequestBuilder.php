<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Support\Str;

trait AjaxRequestBuilder
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $buttonSelectors = [];

    protected $fn;

    protected $javascripts = [
        'fetching' => [],
        'fetched'  => [],
    ];

    /**
     * @param string $url
     * @return $this
     */
    public function request(string $url)
    {
        return $this->setUrl($url);
    }

    public function requestCurrent(array $query = [])
    {
        $this->url = url(request()->getPathInfo()).'?'.http_build_query($query);

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->url = admin_url($url);

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array
     */
    public function getJavascripts()
    {
        return $this->javascripts;
    }

    /**
     * 绑定重新获取数据按钮css选择器
     *
     * @param string|array $selector
     * @return $this
     */
    public function refetch($selector)
    {
        $this->buttonSelectors = 
            array_merge($this->buttonSelectors, (array)$selector);

        return $this;
    }

    /**
     * @return array
     */
    public function getButtonSelectors()
    {
        return $this->buttonSelectors;
    }

    public function fetching(string $script)
    {
        $this->javascripts['fetching'][] = $script;

        return $this;
    }

    public function fetched(string $script)
    {
        $this->javascripts['fetched'][] = $script;

        return $this;
    }

    public function allowBuildFetchingScript()
    {
        return $this->url === null ? false : true;
    }

    public function buildFetchingScript()
    {
        if (!$this->allowBuildFetchingScript()) {
            return false;
        }

        $this->fn = 'frd_'.Str::random(8);

        $fetching = join(';', $this->javascripts['fetching']);
        $fetched  = join(';', $this->javascripts['fetched']);

        $binding = '';
        foreach ($this->buttonSelectors as $v) {
            $binding .= "$('{$v}').click(function () { {$this->fn}($(this).data()) });";
        }

        return <<<SCRIPT
window.{$this->fn} = function (p) {
    $fetching;     
    $.getJSON('{$this->url}', $.extend({_token:LA.token}, p || {}), function (result) {
        {$fetched};
    });
}
{$this->fn}();
$binding;
SCRIPT;

    }

    /**
     * @param AjaxRequestBuilder $fetcher
     * @return $this
     */
    public function copy($fetcher)
    {
        $this->url = $fetcher->getUrl();

        $this->buttonSelectors = $fetcher->getButtonSelectors();

        $scripts = $fetcher->getJavascripts();

        $this->javascripts['fetching'] = array_merge($this->javascripts['fetching'], $scripts['fetching']);
        $this->javascripts['fetched']  = array_merge($this->javascripts['fetched'], $scripts['fetched']);

        return $this;
    }

}
