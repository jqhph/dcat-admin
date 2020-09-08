<?php

namespace Dcat\Admin\Support;

use Dcat\Admin\Models\Setting as Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

class Setting extends Fluent
{
    public static function from()
    {
        return new static(Model::pluck('value', 'slug')->toArray());
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }
}
