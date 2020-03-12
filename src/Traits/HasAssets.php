<?php

namespace Dcat\Admin\Traits;

trait HasAssets
{
    /**
     * @var array
     */
    protected static $html = [];

    /**
     * @param string $html
     *
     * @return null|string
     */
    public static function html($html = '')
    {
        if (! empty($html)) {
            static::$html = array_merge(static::$html, (array) $html);

            return;
        }

        return implode('', array_unique(static::$html));
    }

    /**
     * @return \Dcat\Admin\Layout\Assets
     */
    public static function assets()
    {
        return app('admin.assets');
    }

    /**
     * @param string $name
     */
    public static function collectAssets(string $name)
    {
        static::assets()->withName($name);
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
        static::assets()->css($css);
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
        static::assets()->baseCss($css);
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
        static::assets()->js($js);
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
        static::assets()->headerJs($js);
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
        static::assets()->baseJs($js);
    }

    /**
     * @param string $script
     *
     * @return void
     */
    public static function script($script)
    {
        static::assets()->script($script);
    }

    /**
     * @param string $style
     *
     * @return void
     */
    public static function style($style)
    {
        static::assets()->style($style);
    }
}
