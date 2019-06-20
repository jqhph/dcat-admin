<?php

namespace Dcat\Admin\Traits;

trait BuilderEvents
{
    /**
     * @var callable
     */
    protected static $resolvingListneners = [];

    /**
     * @var callable
     */
    protected static $composerListneners = [];

    /**
     * Register a resolving listener.
     *
     * @param callable $callback
     * @param bool $once
     */
    public static function resolving(callable $callback, bool $once = false)
    {
        static::$resolvingListneners[] = [$callback, $once];
    }

    /**
     * @param array ...$params
     */
    protected function callResolving(...$params)
    {
        $this->callBuilderListeners(static::$resolvingListneners, ...$params);
    }

    /**
     * Register a composing listener.
     *
     * @param callable $callback
     * @param bool $once
     */
    public static function composing(callable $callback, bool $once = false)
    {
        static::$composerListneners[] = [$callback, $once];
    }

    /**
     * @param array ...$params
     */
    protected function callComposing(...$params)
    {
        $this->callBuilderListeners(static::$composerListneners, ...$params);
    }

    /**
     * @param $listeners
     * @param array ...$params
     */
    protected function callBuilderListeners(&$listeners, ...$params)
    {
        foreach ($listeners as $k => $listener) {
            list($callback, $once) = $listener;

            if ($once) {
                unset($listeners[$k]);
            }

            call_user_func($callback, $this, ...$params);
        }
    }

}
