<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

trait HasHtmlAttributes
{
    protected $htmlAttributes = [];

    public function defaultHtmlAttribute($attribute, $value)
    {
        if (! array_key_exists($attribute, $this->htmlAttributes)) {
            $this->setHtmlAttribute($attribute, $value);
        }

        return $this;
    }

    public function setHtmlAttribute($key, $value = null)
    {
        if (is_array($key)) {
            $this->htmlAttributes = array_merge($this->htmlAttributes, $key);

            return $this;
        }
        $this->htmlAttributes[$key] = $value;

        return $this;
    }

    public function appendHtmlAttribute($key, $value)
    {
        $result = $this->getHtmlAttribute($key);

        if (is_array($result)) {
            $result[] = $value;
        } else {
            $result = "{$result} {$value}";
        }

        return $this->setHtmlAttribute($key, $result);
    }

    public function forgetHtmlAttribute($keys)
    {
        Arr::forget($this->htmlAttributes, $keys);

        return $this;
    }

    public function getHtmlAttributes()
    {
        return $this->htmlAttributes;
    }

    public function getHtmlAttribute($key, $default = null)
    {
        return $this->htmlAttributes[$key] ?? $default;
    }

    public function hasHtmlAttribute($key)
    {
        return array_key_exists($key, $this->htmlAttributes);
    }

    public function formatHtmlAttributes()
    {
        return Helper::buildHtmlAttributes($this->htmlAttributes);
    }
}
