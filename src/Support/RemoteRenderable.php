<?php

namespace Dcat\Admin\Support;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

abstract class RemoteRenderable implements Renderable
{
    protected static $js = [];

    protected static $css = [];

    protected $key;

    public function __construct($key = null)
    {
        $this->setKey($key);
    }

    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getUrl()
    {
        $data = [
            'key'        => $this->key,
            'renderable' => str_replace('\\', '_', static::class),
        ];

        return route(admin_api_route('render'), $data);
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
}
