<?php

namespace Dcat\Admin\Traits;

trait HasBuilderEvents
{
    /**
     * Register a resolving event.
     *
     * @param callable $callback
     * @param bool     $once
     */
    public static function resolving(callable $callback, bool $once = false)
    {
        static::setListeners('builder.resolving', $callback, $once);
    }

    /**
     * Call the resolving callbacks.
     *
     * @param array ...$params
     */
    protected function callResolving(...$params)
    {
        $this->callBuilderListeners('builder.resolving', ...$params);
    }

    /**
     * Register a composing event.
     *
     * @param callable $callback
     * @param bool     $once
     */
    public static function composing(callable $callback, bool $once = false)
    {
        static::setListeners('builder.composing', $callback, $once);
    }

    /**
     * Call the composing callbacks.
     *
     * @param array ...$params
     */
    protected function callComposing(...$params)
    {
        $this->callBuilderListeners('builder.composing', ...$params);
    }

    /**
     * Register a composed event.
     *
     * @param callable $callback
     * @param bool     $once
     */
    public static function composed(callable $callback, bool $once = false)
    {
        static::setListeners('builder.composed', $callback, $once);
    }

    /**
     * Call the composed callbacks.
     *
     * @param array ...$params
     */
    protected function callComposed(...$params)
    {
        $this->callBuilderListeners('builder.composed', ...$params);
    }

    /**
     * @param $listeners
     * @param array ...$params
     */
    protected function callBuilderListeners($key, ...$params)
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

    /**
     * @param string   $key
     * @param callable $callback
     * @param bool     $once
     */
    protected static function setListeners($key, $callback, $once)
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
