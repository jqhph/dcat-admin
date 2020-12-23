<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Illuminate\Support\Str;

class Map extends Field
{
    /**
     * Column name.
     *
     * @var array
     */
    protected $column = [];

    /**
     * @var string
     */
    protected $height = '300px';

    /**
     * Get assets required by this field.
     *
     * @return void
     */
    public static function requireAssets()
    {
        $keys = config('admin.map.keys');

        switch (static::getUsingMap()) {
            case 'tencent':
                $js = '//map.qq.com/api/js?v=2.exp&key='.($keys['tencent'] ?? env('TENCENT_MAP_API_KEY'));
                break;
            case 'google':
                $js = '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key='.($keys['google'] ?? env('GOOGLE_API_KEY'));
                break;
            case 'yandex':
                $js = '//api-maps.yandex.ru/2.1/?lang=ru_RU';
                break;
            case 'baidu':
            default:
                $js = '//api.map.baidu.com/getscript?v=2.0&ak='.($keys['baidu'] ?? env('BAIDU_MAP_API_KEY'));
        }

        Admin::js($js);
    }

    public function __construct($column, $arguments)
    {
        $this->column['lat'] = (string) $column;
        $this->column['lng'] = (string) $arguments[0];

        array_shift($arguments);

        $this->label = $this->formatLabel($arguments);

        /*
         * Google map is blocked in mainland China
         * people in China can use Tencent map instead(;
         */
        switch (static::getUsingMap()) {
            case 'tencent':
                $this->tencent();
                break;
            case 'google':
                $this->google();
                break;
            case 'yandex':
                $this->yandex();
                break;
            case 'baidu':
            default:
                $this->baidu();
        }
    }

    public function height(string $height)
    {
        $this->height = $height;

        return $this;
    }

    protected static function getUsingMap()
    {
        return config('admin.map.provider') ?: config('admin.map_provider');
    }

    public function google()
    {
        return $this->addVariables(['type' => 'google']);
    }

    public function tencent()
    {
        return $this->addVariables(['type' => 'tencent']);
    }

    public function yandex()
    {
        return $this->addVariables(['type' => 'yandex']);
    }

    public function baidu()
    {
        return $this->addVariables(['type' => 'baidu', 'searchId' => 'bdmap'.Str::random()]);
    }

    protected function getDefaultElementClass()
    {
        $class = $this->normalizeElementClass($this->column['lat']).$this->normalizeElementClass($this->column['lng']);

        return [$class, static::NORMAL_CLASS];
    }

    public function render()
    {
        $this->addVariables(['height' => $this->height]);

        return parent::render();
    }
}
