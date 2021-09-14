<?php

namespace Dcat\Admin\Support;

use Composer\Autoload\ClassLoader;

class Composer
{
    /**
     * @var array
     */
    protected static $files = [];

    /**
     * @var ClassLoader
     */
    protected static $loader;

    /**
     * 获取 composer 类加载器.
     *
     * @return ClassLoader
     */
    public static function loader()
    {
        if (! static::$loader) {
            static::$loader = include base_path().'/vendor/autoload.php';
        }

        return static::$loader;
    }

    /**
     * @param $path
     * @return ComposerProperty
     */
    public static function parse(?string $path)
    {
        return new ComposerProperty(static::fromJson($path));
    }

    /**
     * @param  null|string  $packageName
     * @param  null|string  $lockFile
     * @return null
     */
    public static function getVersion(?string $packageName, ?string $lockFile = null)
    {
        if (! $packageName) {
            return null;
        }

        $lockFile = $lockFile ?: base_path('composer.lock');

        $content = collect(static::fromJson($lockFile)['packages'] ?? [])
            ->filter(function ($value) use ($packageName) {
                return $value['name'] == $packageName;
            })->first();

        return $content['version'] ?? null;
    }

    /**
     * @param  null|string  $path
     * @return array
     */
    public static function fromJson(?string $path)
    {
        if (isset(static::$files[$path])) {
            return static::$files[$path];
        }

        if (! $path || ! is_file($path)) {
            return static::$files[$path] = [];
        }

        try {
            return static::$files[$path] = (array) json_decode(app('files')->get($path), true);
        } catch (\Throwable $e) {
        }

        return static::$files[$path] = [];
    }
}
