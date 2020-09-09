<?php

namespace Dcat\Admin\Support;

use Dcat\Admin\Models\Setting as Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

class Setting extends Fluent
{
    /**
     * 获取配置.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * 获取启用的扩展.
     *
     * @return array
     */
    public function getExtensionsEnabled()
    {
        $value = $this->get('extensions_enabled') ?: '[]';

        $value = json_decode($value, true);

        return $value ?: [];
    }

    /**
     * @return static
     */
    public static function fromDatabase()
    {
        $values = [];

        try {
            $values = Model::pluck('value', 'slug')->toArray();
        } catch (QueryException $e) {
        }

        return new static($values);
    }
}
