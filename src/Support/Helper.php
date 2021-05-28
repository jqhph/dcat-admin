<?php

namespace Dcat\Admin\Support;

use Dcat\Admin\Grid;
use Dcat\Laravel\Database\WhereHasInServiceProvider;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Helper
{
    /**
     * @var array
     */
    public static $fileTypes = [
        'image'      => 'png|jpg|jpeg|tmp|gif',
        'word'       => 'doc|docx',
        'excel'      => 'xls|xlsx|csv',
        'powerpoint' => 'ppt|pptx',
        'pdf'        => 'pdf',
        'code'       => 'php|js|java|python|ruby|go|c|cpp|sql|m|h|json|html|aspx',
        'archive'    => 'zip|tar\.gz|rar|rpm',
        'txt'        => 'txt|pac|log|md',
        'audio'      => 'mp3|wav|flac|3pg|aa|aac|ape|au|m4a|mpc|ogg',
        'video'      => 'mkv|rmvb|flv|mp4|avi|wmv|rm|asf|mpeg',
    ];

    protected static $controllerNames = [];

    /**
     * 把给定的值转化为数组.
     *
     * @param $value
     * @param bool $filter
     *
     * @return array
     */
    public static function array($value, bool $filter = true): array
    {
        if ($value === null || $value === '' || $value === []) {
            return [];
        }

        if ($value instanceof \Closure) {
            $value = $value();
        }

        if (is_array($value)) {
        } elseif ($value instanceof Jsonable) {
            $value = json_decode($value->toJson(), true);
        } elseif ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif (is_string($value)) {
            $array = null;

            try {
                $array = json_decode($value, true);
            } catch (\Throwable $e) {
            }

            $value = is_array($array) ? $array : explode(',', $value);
        } else {
            $value = (array) $value;
        }

        return $filter ? array_filter($value, function ($v) {
            return $v !== '' && $v !== null;
        }) : $value;
    }

    /**
     * 把给定的值转化为字符串.
     *
     * @param string|Grid|\Closure|Renderable|Htmlable  $value
     * @param array                                     $params
     * @param object                                    $newThis
     *
     * @return string
     */
    public static function render($value, $params = [], $newThis = null): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof \Closure) {
            $newThis && ($value = $value->bindTo($newThis));

            $value = $value(...(array) $params);
        }

        if ($value instanceof Grid) {
            return (string) $value->render();
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
     * 获取当前控制器名称.
     *
     * @return mixed|string
     */
    public static function getControllerName()
    {
        $router = app('router');

        if (! $router->current()) {
            return 'undefined';
        }

        $actionName = $router->current()->getActionName();

        if (! isset(static::$controllerNames[$actionName])) {
            $controller = class_basename(explode('@', $actionName)[0]);

            static::$controllerNames[$actionName] = str_replace('Controller', '', $controller);
        }

        return static::$controllerNames[$actionName];
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    public static function buildHtmlAttributes($attributes)
    {
        $html = '';

        foreach ((array) $attributes as $key => &$value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }

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

        parse_str($array[1] ?? '', $originalQuery);

        return $url.'?'.http_build_query(array_merge($originalQuery, $query));
    }

    /**
     * @param string                 $url
     * @param string|array|Arrayable $keys
     *
     * @return string
     */
    public static function urlWithoutQuery($url, $keys)
    {
        if (! Str::contains($url, '?') || ! $keys) {
            return $url;
        }

        if ($keys instanceof Arrayable) {
            $keys = $keys->toArray();
        }

        $keys = (array) $keys;

        $urlInfo = parse_url($url);

        parse_str($urlInfo['query'], $query);

        Arr::forget($query, $keys);

        $baseUrl = explode('?', $url)[0];

        return $query
            ? $baseUrl.'?'.http_build_query($query)
            : $baseUrl;
    }

    /**
     * @param Arrayable|array|string $keys
     *
     * @return string
     */
    public static function fullUrlWithoutQuery($keys)
    {
        return static::urlWithoutQuery(request()->fullUrl(), $keys);
    }

    /**
     * @param string       $url
     * @param string|array $keys
     *
     * @return bool
     */
    public static function urlHasQuery(string $url, $keys)
    {
        $value = explode('?', $url);

        if (empty($value[1])) {
            return false;
        }

        parse_str($value[1], $query);

        foreach ((array) $keys as $key) {
            if (Arr::has($query, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 匹配请求路径.
     *
     * @example
     *      Helper::matchRequestPath(admin_base_path('auth/user'))
     *      Helper::matchRequestPath(admin_base_path('auth/user*'))
     *      Helper::matchRequestPath(admin_base_path('auth/user/* /edit'))
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

        // 判断路由名称
        if ($request->routeIs($path) || $request->routeIs(admin_route_name($path))) {
            return true;
        }

        if (! Str::contains($path, '*')) {
            return $path === $current;
        }

        $path = str_replace(['*', '/'], ['([0-9a-z-_,])*', "\/"], $path);

        return preg_match("/$path/i", $current);
    }

    /**
     * 生成层级数据.
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
            $pk = $node[$parentKeyName];
            $pk = is_numeric($pk) ? (int) $pk : $pk;

            if ($pk === $parentId) {
                $children = static::buildNestedArray(
                    $nodes,
                    $node[$primaryKeyName],
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
     * @param string $name
     * @param string $symbol
     *
     * @return mixed
     */
    public static function slug(string $name, string $symbol = '-')
    {
        $text = preg_replace_callback('/([A-Z])/', function ($text) use ($symbol) {
            return $symbol.strtolower($text[1]);
        }, $name);

        return str_replace('_', $symbol, ltrim($text, $symbol));
    }

    /**
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
     * @param array $array
     *
     * @return string
     */
    public static function exportArrayPhp(array $array)
    {
        return "<?php \nreturn ".static::exportArray($array).";\n";
    }

    /**
     * 删除数组中的元素.
     *
     * @param array $array
     * @param mixed $value
     * @param bool $strict
     */
    public static function deleteByValue(&$array, $value, bool $strict = false)
    {
        $value = (array) $value;

        foreach ($array as $index => $item) {
            if (in_array($item, $value, $strict)) {
                unset($array[$index]);
            }
        }
    }

    /**
     * 颜色转亮.
     *
     * @param string $color
     * @param int    $amt
     *
     * @return string
     */
    public static function colorLighten(string $color, int $amt)
    {
        if (! $amt) {
            return $color;
        }

        $hasPrefix = false;

        if (mb_strpos($color, '#') === 0) {
            $color = mb_substr($color, 1);

            $hasPrefix = true;
        }

        [$red, $blue, $green] = static::colorToRBG($color, $amt);

        return ($hasPrefix ? '#' : '').dechex($green + ($blue << 8) + ($red << 16));
    }

    /**
     * 颜色转暗.
     *
     * @param string $color
     * @param int    $amt
     *
     * @return string
     */
    public static function colorDarken(string $color, int $amt)
    {
        return static::colorLighten($color, -$amt);
    }

    /**
     * 颜色透明度.
     *
     * @param string       $color
     * @param float|string $alpha
     *
     * @return string
     */
    public static function colorAlpha(string $color, $alpha)
    {
        if ($alpha >= 1) {
            return $color;
        }

        if (mb_strpos($color, '#') === 0) {
            $color = mb_substr($color, 1);
        }

        [$red, $blue, $green] = static::colorToRBG($color);

        return "rgba($red, $blue, $green, $alpha)";
    }

    /**
     * @param string $color
     * @param int    $amt
     *
     * @return array
     */
    public static function colorToRBG(string $color, int $amt = 0)
    {
        $format = function ($value) {
            if ($value > 255) {
                return 255;
            }
            if ($value < 0) {
                return 0;
            }

            return $value;
        };

        $num = hexdec($color);

        $red = $format(($num >> 16) + $amt);
        $blue = $format((($num >> 8) & 0x00FF) + $amt);
        $green = $format(($num & 0x0000FF) + $amt);

        return [$red, $blue, $green];
    }

    /**
     * 验证扩展包名称.
     *
     * @param string $name
     *
     * @return int
     */
    public static function validateExtensionName($name)
    {
        return preg_match('/^[\w\-_]+\/[\w\-_]+$/', $name);
    }

    /**
     * Get file icon.
     *
     * @param string $file
     *
     * @return string
     */
    public static function getFileIcon($file = '')
    {
        $extension = File::extension($file);

        foreach (static::$fileTypes as $type => $regex) {
            if (preg_match("/^($regex)$/i", $extension) !== 0) {
                return "fa fa-file-{$type}-o";
            }
        }

        return 'fa fa-file-o';
    }

    /**
     * 判断是否是ajax请求.
     *
     * @param Request $request
     *
     * @return bool
     */
    public static function isAjaxRequest(?Request $request = null)
    {
        /* @var Request $request */
        $request = $request ?: request();

        return $request->ajax() && ! $request->pjax();
    }

    /**
     * 判断是否是IE浏览器.
     *
     * @return false|int
     */
    public static function isIEBrowser()
    {
        return (bool) preg_match('/Mozilla\/5\.0 \(Windows NT 10\.0; WOW64; Trident\/7\.0; rv:[0-9\.]*\) like Gecko/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
    }

    /**
     * 判断是否QQ浏览器.
     *
     * @return bool
     */
    public static function isQQBrowser()
    {
        return mb_strpos(mb_strtolower($_SERVER['HTTP_USER_AGENT'] ?? ''), 'qqbrowser') !== false;
    }

    /**
     * @param string $url
     *
     * @return void
     */
    public static function setPreviousUrl($url)
    {
        session()->flash('admin.prev.url', static::urlWithoutQuery((string) $url, '_pjax'));
    }

    /**
     * @return string
     */
    public static function getPreviousUrl()
    {
        return (string) (session()->get('admin.prev.url') ? url(session()->get('admin.prev.url')) : url()->previous());
    }

    /**
     * @param mixed $command
     * @param int   $timeout
     * @param null  $input
     * @param null  $cwd
     *
     * @return Process
     */
    public static function process($command, $timeout = 100, $input = null, $cwd = null)
    {
        $parameters = [
            $command,
            $cwd,
            [],
            $input,
            $timeout,
        ];

        return is_string($command)
            ? Process::fromShellCommandline(...$parameters)
            : new Process(...$parameters);
    }

    /**
     * 判断两个值是否相等.
     *
     * @param $value1
     * @param $value2
     *
     * @return bool
     */
    public static function equal($value1, $value2)
    {
        if ($value1 === null || $value2 === null) {
            return false;
        }

        if (! is_scalar($value1) || ! is_scalar($value2)) {
            return $value1 === $value2;
        }

        return (string) $value1 === (string) $value2;
    }

    /**
     * 判断给定的数组是是否包含给定元素.
     *
     * @param mixed $value
     * @param array $array
     *
     * @return bool
     */
    public static function inArray($value, array $array)
    {
        $array = array_map(function ($v) {
            if (is_scalar($v) || $v === null) {
                $v = (string) $v;
            }

            return $v;
        }, $array);

        return in_array((string) $value, $array, true);
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    public static function strLimit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')).$end;
    }

    /**
     * 获取类名或对象的文件路径.
     *
     * @param string|object $class
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    public static function guessClassFileName($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        try {
            if (class_exists($class)) {
                return (new \ReflectionClass($class))->getFileName();
            }
        } catch (\Throwable $e) {
        }

        $class = trim($class, '\\');

        $composer = Composer::parse(base_path('composer.json'));

        $map = collect($composer->autoload['psr-4'] ?? [])->mapWithKeys(function ($path, $namespace) {
            $namespace = trim($namespace, '\\').'\\';

            return [$namespace => [$namespace, $path]];
        })->sortBy(function ($_, $namespace) {
            return strlen($namespace);
        }, SORT_REGULAR, true);

        $prefix = explode($class, '\\')[0];

        if ($map->isEmpty()) {
            if (Str::startsWith($class, 'App\\')) {
                $values = ['App\\', 'app/'];
            }
        } else {
            $values = $map->filter(function ($_, $k) use ($class) {
                return Str::startsWith($class, $k);
            })->first();
        }

        if (empty($values)) {
            $values = [$prefix.'\\', self::slug($prefix).'/'];
        }

        [$namespace, $path] = $values;

        return base_path(str_replace([$namespace, '\\'], [$path, '/'], $class)).'.php';
    }

    /**
     * Is input data is has-one relation.
     *
     * @param Collection $fields
     * @param array      $input
     */
    public static function prepareHasOneRelation(Collection $fields, array &$input)
    {
        $relations = [];
        $fields->each(function ($field) use (&$relations) {
            $column = $field->column();

            if (is_array($column)) {
                foreach ($column as $v) {
                    if (Str::contains($v, '.')) {
                        $first = explode('.', $v)[0];
                        $relations[$first] = null;
                    }
                }

                return;
            }

            if (Str::contains($column, '.')) {
                $first = explode('.', $column)[0];
                $relations[$first] = null;
            }
        });

        foreach ($relations as $first => $v) {
            if (isset($input[$first])) {
                foreach ($input[$first] as $key => $value) {
                    if (is_array($value)) {
                        $input["$first.$key"] = $value;
                    }
                }

                $input = array_merge($input, Arr::dot([$first => $input[$first]]));
            }
        }
    }

    /**
     * 设置查询条件.
     *
     * @param mixed $model
     * @param string $column
     * @param string $query
     * @param mixed array $params
     *
     * @return void
     */
    public static function withQueryCondition($model, ?string $column, string $query, array $params)
    {
        if (! Str::contains($column, '.')) {
            $model->$query($column, ...$params);

            return;
        }

        $method = $query === 'orWhere' ? 'orWhere' : 'where';
        $subQuery = $query === 'orWhere' ? 'where' : $query;

        $model->$method(function ($q) use ($column, $subQuery, $params) {
            static::withRelationQuery($q, $column, $subQuery, $params);
        });
    }

    /**
     * 设置关联关系查询条件.
     *
     * @param mixed $model
     * @param string $column
     * @param string $query
     * @param mixed ...$params
     *
     * @return void
     */
    public static function withRelationQuery($model, ?string $column, string $query, array $params)
    {
        $column = explode('.', $column);

        array_unshift($params, array_pop($column));

        // 增加对whereHasIn的支持
        $method = class_exists(WhereHasInServiceProvider::class) ? 'whereHasIn' : 'whereHas';

        $model->$method(implode('.', $column), function ($relation) use ($params, $query) {
            $relation->$query(...$params);
        });
    }

    /**
     * Html转义.
     *
     * @param array|string $item
     *
     * @return mixed
     */
    public static function htmlEntityEncode($item)
    {
        if (is_object($item)) {
            return $item;
        }
        if (is_array($item)) {
            array_walk_recursive($item, function (&$value) {
                $value = htmlentities($value);
            });
        } else {
            $item = htmlentities($item);
        }

        return $item;
    }

    /**
     * 格式化表单元素 name 属性.
     *
     * @param string|array $name
     *
     * @return mixed|string
     */
    public static function formatElementName($name)
    {
        if (! $name) {
            return $name;
        }

        if (is_array($name)) {
            foreach ($name as &$v) {
                $v = static::formatElementName($v);
            }

            return $name;
        }

        $name = explode('.', $name);

        if (count($name) == 1) {
            return $name[0];
        }

        $html = array_shift($name);
        foreach ($name as $piece) {
            $html .= "[$piece]";
        }

        return $html;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array|\ArrayAccess  $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public static function arraySet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);
        $default = null;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || (! is_array($array[$key]) && ! $array[$key] instanceof \ArrayAccess)) {
                $array[$key] = [];
            }

            if (is_array($array)) {
                $array = &$array[$key];
            } else {
                if (is_object($array[$key])) {
                    $array[$key] = static::arraySet($array[$key], implode('.', $keys), $value);
                } else {
                    $mid = $array[$key];

                    $array[$key] = static::arraySet($mid, implode('.', $keys), $value);
                }
            }
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * 把下划线风格字段名转化为驼峰风格.
     *
     * @param array $array
     *
     * @return array
     */
    public static function camelArray(array &$array)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                Helper::camelArray($v);
            }

            $array[Str::camel($k)] = $v;
        }

        return $array;
    }

    /**
     * 获取文件名称.
     *
     * @param string $name
     *
     * @return array|mixed
     */
    public static function basename($name)
    {
        if (! $name) {
            return $name;
        }

        return last(explode('/', $name));
    }

    /**
     * @param string|int $key
     * @param array|object $arrayOrObject
     *
     * @return bool
     */
    public static function keyExists($key, $arrayOrObject)
    {
        if (is_object($arrayOrObject)) {
            $arrayOrObject = static::array($arrayOrObject, false);
        }

        return array_key_exists($key, $arrayOrObject);
    }
}
