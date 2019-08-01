<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;

trait HasHtmlAttributes
{
    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setHtmlAttribute($key, $value = null)
    {
        if (is_array($key)) {
            $this->htmlAttributes = array_merge($this->htmlAttributes, $key);

            return $this;
        }
        $this->htmlAttributes[$key] = $value;

        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return |null
     */
    public function getHtmlAttribute($key, $default = null)
    {
        return $this->htmlAttributes[$key] ?? $default;
    }

    /**
     * @return string
     */
    public function formatHtmlAttribute()
    {
        return Helper::buildHtmlAttributes($this->htmlAttributes);
    }

}

