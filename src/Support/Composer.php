<?php

namespace Dcat\Admin\Support;

class Composer
{
    /**
     * @var array
     */
    protected static $files = [];

    /**
     * @param $path
     *
     * @return ComposerProperty
     */
    public static function parse(?string $path)
    {
        return new ComposerProperty(static::readJson($path));
    }

    /**
     * @param null|string $packageName
     * @param null|string $lockFile
     *
     * @return null
     */
    public static function getVersion(?string $packageName, ?string $lockFile = null)
    {
        if (! $packageName) {
            return null;
        }

        $lockFile = $lockFile ?: base_path('composer.lock');

        $content = collect(static::readJson($lockFile)['packages'] ?? [])
            ->filter(function ($value) use ($packageName) {
                return $value['name'] == $packageName;
            })->first();

        return $content['version'] ?? null;
    }

    /**
     * @param null|string $path
     *
     * @return array
     */
    public static function readJson(?string $path)
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
