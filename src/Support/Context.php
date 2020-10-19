<?php

namespace Dcat\Admin\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

/**
 * Class Context.
 *
 * @property string $favicon
 * @property string $metaTitle
 * @property string $pjaxContainerId
 * @property array|null $html
 * @property array|null $ignoreQueries
 * @property array|null $jsVariables
 */
class Context extends Fluent
{
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    public function forget($keys)
    {
        Arr::forget($this->attributes, $keys);
    }

    public function flush()
    {
        $this->attributes = [];
    }
}
