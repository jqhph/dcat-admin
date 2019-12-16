<?php

namespace Dcat\Admin\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class Helper
{
    /**
     * Update extension config.
     *
     * @param array $config
     *
     * @return bool
     */
    public static function updateExtensionConfig(array $config)
    {
        $files = app('files');
        $result = (bool) $files->put(config_path('admin-extensions.php'), self::exportArrayPhp($config));

        if ($result && is_file(base_path('bootstrap/cache/config.php'))) {
            Artisan::call('config:cache');
        }

        config(['admin-extensions' => $config]);

        return $result;
    }

    /**
     * Converts the given value to an array.
     *
     * @param $value
     * @param bool $filter
     *
     * @return array
     */
    public static function array($value, bool $filter = true): array
    {
        if (! $value) {
            return [];
        }

        if ($value instanceof \Closure) {
            $value = $value();
        }

        if (is_array($value)) {
        } elseif ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif (is_string($value)) {
            $value = explode(',', $value);
        } else {
            $value = (array) $value;
        }

        return $filter ? array_filter($value, function ($v) {
            return $v !== '' && $v !== null;
        }) : $value;
    }

    /**
     * Converts the given value to string.
     *
     * @param mixed  $value
     * @param array  $params
     * @param object $newThis
     *
     * @return string
     */
    public static function render($value, $params = [], $newThis = null): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof \Closure) {
            $newThis && $value = $value->bindTo($newThis);

            $value = $value(...(array) $params);
        }

        if ($value instanceof Renderable) {
            return (string) $value->render();
        }

        if ($value instanceof Htmlable) {
            return (string) $value->toHtml();
        }

        return (string) $value;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param array $attributes
     *
     * @return string
     */
    public static function buildHtmlAttributes($attributes)
    {
        $html = '';

        foreach ((array) $attributes as $key => &$value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            $element = '';

            if ($value !== null) {
                $element = $key.'="'.htmlentities($value, ENT_QUOTES, 'UTF-8').'"';
            }

            $html .= $element;
        }

        return $html;
    }

    /**
     * Get url with the added query string parameters.
     *
     * @param string $url
     * @param array  $query
     *
     * @return string
     */
    public static function urlWithQuery(?string $url, array $query = [])
    {
        if (! $url || ! $query) {
            return $url;
        }

        $array = explode('?', $url);

        $url = $array[0];

        $originalQuery = $array[1] ?? '';

        parse_str($originalQuery, $originalQuery);

        return $url.'?'.http_build_query(array_merge($originalQuery, $query));
    }

    /**
     * If a request match the specific path.
     *
     * @example
     *      Helper::matchRequestPath('auth/user')
     *      Helper::matchRequestPath('auth/user*')
     *      Helper::matchRequestPath('auth/user/* /edit')
     *      Helper::matchRequestPath('GET,POST:auth/user')
     *
     * @param string      $path
     * @param null|string $current
     *
     * @return bool
     */
    public static function matchRequestPath($path, ?string $current = null)
    {
        $request = request();
        $current = $current ?: $request->decodedPath();

        if (Str::contains($path, ':')) {
            [$methods, $path] = explode(':', $path);

            $methods = array_map('strtoupper', explode(',', $methods));

            if (! empty($methods) && ! in_array($request->method(), $methods)) {
                return false;
            }
        }

        if (! Str::contains($path, '*')) {
            return $path === $current;
        }

        $path = str_replace(['*', '/'], ['([0-9a-z-_,])*', "\/"], $path);

        return preg_match("/$path/i", $current);
    }

    /**
     * Build nested array.
     *
     * @param array       $nodes
     * @param int         $parentId
     * @param string|null $primaryKeyName
     * @param string|null $parentKeyName
     * @param string|null $childrenKeyName
     *
     * @return array
     */
    public static function buildNestedArray(
        $nodes = [],
        $parentId = 0,
        ?string $primaryKeyName = null,
        ?string $parentKeyName = null,
        ?string $childrenKeyName = null
    ) {
        $branch = [];
        $primaryKeyName = $primaryKeyName ?: 'id';
        $parentKeyName = $parentKeyName ?: 'parent_id';
        $childrenKeyName = $childrenKeyName ?: 'children';

        $parentId = is_numeric($parentId) ? (int) $parentId : $parentId;

        foreach ($nodes as $node) {
            $pk = Arr::get($node, $parentKeyName);
            $pk = is_numeric($pk) ? (int) $pk : $pk;

            if ($pk === $parentId) {
                $children = static::buildNestedArray(
                    $nodes,
                    Arr::get($node, $primaryKeyName),
                    $primaryKeyName,
                    $parentKeyName,
                    $childrenKeyName
                );

                if ($children) {
                    $node[$childrenKeyName] = $children;
                }
                $branch[] = $node;
            }
        }

        return $branch;
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $name
     * @param string $symbol
     *
     * @return mixed
     */
    public static function slug(string $name, string $symbol = '-')
    {
        $text = preg_replace_callback('/([A-Z])/', function (&$text) use ($symbol) {
            return $symbol.strtolower($text[1]);
        }, $name);

        return str_replace('_', $symbol, ltrim($text, $symbol));
    }

    /**
     * 把php数据转化成文本形式.
     *
     * @param array $array
     * @param int   $level
     *
     * @return string
     */
    public static function exportArray(array &$array, $level = 1)
    {
        $start = '[';
        $end = ']';

        $txt = "$start\n";

        foreach ($array as $k => &$v) {
            if (is_array($v)) {
                $pre = is_string($k) ? "'$k' => " : "$k => ";

                $txt .= str_repeat(' ', $level * 4).$pre.static::exportArray($v, $level + 1).",\n";

                continue;
            }
            $t = $v;

            if ($v === true) {
                $t = 'true';
            } elseif ($v === false) {
                $t = 'false';
            } elseif ($v === null) {
                $t = 'null';
            } elseif (is_string($v)) {
                $v = str_replace("'", "\\'", $v);
                $t = "'$v'";
            }

            $pre = is_string($k) ? "'$k' => " : "$k => ";

            $txt .= str_repeat(' ', $level * 4)."{$pre}{$t},\n";
        }

        return $txt.str_repeat(' ', ($level - 1) * 4).$end;
    }

    /**
     * 把php数据转化成文本形式，并以"return"形式返回.
     *
     * @param array $array
     *
     * @return string
     */
    public static function exportArrayPhp(array $array)
    {
        return "<?php \nreturn ".static::exportArray($array).";\n";
    }

    /**
     * Delete from array by value.
     *
     * @param array $array
     * @param mixed $value
     */
    public static function deleteByValue(&$array, $value)
    {
        $value = (array) $value;

        foreach ($array as $index => $item) {
            if (in_array($item, $value)) {
                unset($array[$index]);
            }
        }
    }
}
