<?php

namespace Dcat\Admin\Support;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

abstract class RemoteRenderable implements Renderable
{
    protected static $js = [];

    protected static $css = [];

    protected $variables = [];

    public function __construct($key = null)
    {
        $this->with($key);
    }

    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->variables = array_merge($this->variables, $key);
        } elseif ($key !== null) {
            $this->variables[$key] = $value;
        }

        return $this;
    }

    public function getUrl()
    {
        $data = array_merge($this->variables(), [
            'renderable' => str_replace('\\', '_', static::class),
        ]);

        return route(admin_api_route('render'), $data);
    }

    public function variables()
    {
        return $this->variables;
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
        return $this->variables[$name] ?? null;
    }
}
