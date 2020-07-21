<?php

namespace Dcat\Admin\Support;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

abstract class LazyRenderable implements Renderable
{
    protected static $js = [];

    protected static $css = [];

    protected $parameters = [];

    public function __construct(array $parameters = null)
    {
        $this->with($parameters);
    }

    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->parameters = array_merge($this->parameters, $key);
        } elseif ($key !== null) {
            $this->parameters[$key] = $value;
        }

        return $this;
    }

    public function getUrl()
    {
        $data = array_merge($this->parameters(), [
            'renderable' => str_replace('\\', '_', static::class),
        ]);

        return route(admin_api_route('render'), $data);
    }

    public function parameters()
    {
        return $this->parameters;
    }

    public static function collectAssets()
    {
        Admin::js(static::$js);
        Admin::css(static::$css);
    }

    public static function make(...$params)
    {
        return new static(...$params);
    }

    public function __set($name, $value)
    {
        $this->with($name, $value);
    }

    public function __get($name)
    {
        return $this->parameters[$name] ?? null;
    }
}
