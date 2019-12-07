<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

trait HasHtmlAttributes
{
    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * @param string|array $key
     * @param mixed        $value
     *
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
     * @param string|array $keys
     *
     * @return $this
     */
    public function forgetHtmlAttribute($keys)
    {
        Arr::forget($this->htmlAttributes, $keys);

        return $this;
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return $this->htmlAttributes;
    }

    /**
     * Set default attribute.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function defaultHtmlAttribute($attribute, $value)
    {
        if (! array_key_exists($attribute, $this->htmlAttributes)) {
            $this->setHtmlAttribute($attribute, $value);
        }

        return $this;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return |null
     */
    public function getHtmlAttribute($key, $default = null)
    {
        return $this->htmlAttributes[$key] ?? $default;
    }

    /**
     * @param mixed $key
     *
     * @return |null
     */
    public function hasHtmlAttribute($key)
    {
        return array_key_exists($key, $this->htmlAttributes);
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @return string
     */
    public function formatHtmlAttributes()
    {
        return Helper::buildHtmlAttributes($this->htmlAttributes);
    }
}
