<?php

namespace Dcat\Admin\Traits;

trait HasBuilderEvents
{
    public static function resolving(callable $callback, bool $once = false)
    {
        static::addBuilderListeners('builder.resolving', $callback, $once);
    }

    protected function callResolving(...$params)
    {
        $this->fireBuilderEvent('builder.resolving', ...$params);
    }

    public static function composing(callable $callback, bool $once = false)
    {
        static::addBuilderListeners('builder.composing', $callback, $once);
    }

    protected function callComposing(...$params)
    {
        $this->fireBuilderEvent('builder.composing', ...$params);
    }

    protected function fireBuilderEvent($key, ...$params)
    {
        $storage = app('admin.context');

        $key = static::formatBuilderEventKey($key);

        $listeners = $storage->get($key) ?: [];

        foreach ($listeners as $k => $listener) {
            [$callback, $once] = $listener;

            if ($once) {
                unset($listeners[$k]);
            }

            call_user_func($callback, $this, ...$params);
        }

        $storage[$key] = $listeners;
    }

    protected static function addBuilderListeners($key, $callback, $once)
    {
        $storage = app('admin.context');

        $key = static::formatBuilderEventKey($key);

        $listeners = $storage->get($key) ?: [];

        $listeners[] = [$callback, $once];

        $storage[$key] = $listeners;
    }

    protected static function formatBuilderEventKey($key)
    {
        return static::class.'::'.$key;
    }
}
