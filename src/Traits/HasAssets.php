<?php

namespace Dcat\Admin\Traits;

trait HasAssets
{
    /**
     * @return \Dcat\Admin\Layout\Asset
     */
    public static function asset()
    {
        return app('admin.asset');
    }

    /**
     * @param string|array $name
     * @param array $params
     *
     * @return void
     */
    public static function requireAssets($name, array $params = [])
    {
        static::asset()->require($name, $params);
    }

    /**
     * Add css.
     *
     * @param string|array $css
     *
     * @return void
     */
    public static function css($css)
    {
        static::asset()->css($css);
    }

    /**
     * Set base css.
     *
     * @param array $css
     * @param bool $merge
     */
    public static function baseCss(array $css, bool $merge = true)
    {
        static::asset()->baseCss($css, $merge);
    }

    /**
     * Add js.
     *
     * @param string|array $js
     *
     * @return void
     */
    public static function js($js)
    {
        static::asset()->js($js);
    }

    /**
     * Add js.
     *
     * @param string|array $js
     *
     * @return void
     */
    public static function headerJs($js)
    {
        static::asset()->headerJs($js);
    }

    /**
     * Set base js.
     *
     * @param array $js
     * @param bool $merge
     */
    public static function baseJs(array $js, bool $merge = true)
    {
        static::asset()->baseJs($js, $merge);
    }

    /**
     * @param string $script
     * @param bool   $direct
     *
     * @return void
     */
    public static function script($script, bool $direct = false)
    {
        static::asset()->script($script, $direct);
    }

    /**
     * @param string $style
     *
     * @return void
     */
    public static function style($style)
    {
        static::asset()->style($style);
    }

    /**
     * @param string|array $font
     *
     * @return void
     */
    public static function fonts($font)
    {
        static::asset()->fonts = $font;
    }
}
