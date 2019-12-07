<?php

namespace Dcat\Admin\Traits;

trait HasDefinitions
{
    /**
     * Defined columns.
     *
     * @var array
     */
    protected static $definitions = [];

    /**
     * @param string $name
     * @param mixed  $definition
     */
    public static function define(string $name, $definition)
    {
        if ($name && $definition) {
            static::$definitions[$name] = $definition;
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public static function hasDefinition(string $name)
    {
        return isset(static::$definitions[$name]);
    }
}
