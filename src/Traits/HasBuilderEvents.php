<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Admin;

trait HasBuilderEvents
{
    public static function resolving(callable $callback, bool $once = false)
    {
        static::addBuilderListeners('builder:resolving', $callback, $once);
    }

    protected function callResolving(...$params)
    {
        $this->fireBuilderEvent('builder:resolving', ...$params);
    }

    public static function composing(callable $callback, bool $once = false)
    {
        static::addBuilderListeners('builder:composing', $callback, $once);
    }

    protected function callComposing(...$params)
    {
        $this->fireBuilderEvent('builder:composing', ...$params);
    }

    protected function fireBuilderEvent($key, ...$params)
    {
        $context = Admin::context();

        $key = static::formatEventKey($key);

        $listeners = $context->get($key) ?: [];

        foreach ($listeners as $k => $listener) {
            [$callback, $once] = $listener;

            if ($once) {
                unset($listeners[$k]);
            }

            call_user_func($callback, $this, ...$params);
        }

        $context[$key] = $listeners;
    }

    protected static function addBuilderListeners($key, $callback, $once)
    {
        $context = Admin::context();

        $key = static::formatEventKey($key);

        $listeners = $context->get($key) ?: [];

        $listeners[] = [$callback, $once];

        $context[$key] = $listeners;
    }

    protected static function formatEventKey($key)
    {
        return static::class.':'.$key;
    }
}
