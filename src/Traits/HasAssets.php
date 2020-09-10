<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;

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
     * @param string $name
     *
     * @return void
     */
    public static function collectAssets(string $name)
    {
        static::asset()->collect($name);
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
     *
     * @return array|void
     */
    public static function baseCss(array $css)
    {
        static::asset()->baseCss($css);
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
     *
     * @return void
     */
    public static function baseJs(array $js)
    {
        static::asset()->baseJs($js);
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
