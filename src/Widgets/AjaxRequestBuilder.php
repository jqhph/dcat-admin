<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Support\Str;

trait AjaxRequestBuilder
{
    /**
     * @var string
     */
    protected $__url;

    /**
     * @var array
     */
    protected $buttonSelectors = [];

    /**
     * @var string
     */
    protected $fn;

    /**
     * @var array
     */
    protected $javascripts = [
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
    public function getJavascripts()
    {
        return $this->javascripts;
    }

    /**
     * Set css selectors of refetch links.
     *
     * @param string|array $selector
     *
     * @return $this
     */
    public function refetch($selector)
    {
        $this->buttonSelectors =
            array_merge($this->buttonSelectors, (array) $selector);

        return $this;
    }

    /**
     * @return array
     */
    public function getButtonSelectors()
    {
        return $this->buttonSelectors;
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
        $this->javascripts['fetching'][] = $script;

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
        $this->javascripts['fetched'][] = $script;

        return $this;
    }

    /**
     * @return bool
     */
    public function allowBuildFetchingScript()
    {
        return $this->__url === null ? false : true;
    }

    /**
     * @return bool|string
     */
    public function buildFetchingScript()
    {
        if (! $this->allowBuildFetchingScript()) {
            return false;
        }

        $this->fn = 'frd_'.Str::random(8);

        $fetching = implode(';', $this->javascripts['fetching']);
        $fetched = implode(';', $this->javascripts['fetched']);

        $binding = '';
        foreach ($this->buttonSelectors as $v) {
            $binding .= "$('{$v}').click(function () { {$this->fn}($(this).data()) });";
        }

        return <<<JS
window.{$this->fn} = function (p) {
    $fetching;     
    $.getJSON('{$this->__url}', $.extend({_token:LA.token}, p || {}), function (result) {
        {$fetched};
    });
}
{$this->fn}();
$binding;
JS;
    }

    /**
     * Copy the given AjaxRequestBuilder.
     *
     * @param AjaxRequestBuilder $fetcher
     *
     * @return $this
     */
    public function copy($fetcher)
    {
        $this->__url = $fetcher->getUrl();

        $this->buttonSelectors = $fetcher->getButtonSelectors();

        $scripts = $fetcher->getJavascripts();

        $this->javascripts['fetching'] = array_merge($this->javascripts['fetching'], $scripts['fetching']);
        $this->javascripts['fetched'] = array_merge($this->javascripts['fetched'], $scripts['fetched']);

        return $this;
    }
}
