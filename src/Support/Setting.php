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
     * 设置配置信息.
     *
     * @param array $data
     *
     * @return $this
     */
    public function set($key, $value = null)
    {
        $data = is_array($key) ? $key : [$key => $value];

        foreach ($data as $key => $value) {
            Arr::set($this->attributes, $key, $value);
        }

        return $this;
    }

    /**
     * 保存配置到数据库
     *
     * @param array $data
     *
     * @return $this
     */
    public function save(array $data = [])
    {
        if ($data) {
            $this->set($data);
        }

        foreach ($this->attributes as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $model = Model::query()
                ->where('slug', $key)
                ->first() ?: new Model();

            $model->fill([
                'slug'  => $key,
                'value' => (string) $value,
            ])->save();
        }

        return $this;
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
