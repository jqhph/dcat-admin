<?php

namespace Dcat\Admin\Support;

use Dcat\Admin\Admin;

class Translator
{
    protected static $method;

    /**
     * @var \Illuminate\Contracts\Translation\Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $path;

    public function __construct()
    {
        $this->translator = app('translator');
    }

    /**
     * 设置翻译文件路径.
     *
     * @param  null|string  $path
     * @return void
     */
    public function setPath(?string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        if (! $this->path) {
            $this->path = Admin::context()->translation ?: admin_controller_slug();
        }

        return $this->path;
    }

    /**
     * 翻译字段名称.
     *
     * @param  string  $field
     * @param  null  $locale
     * @return false|mixed|string|string[]
     */
    public function transField(?string $field, $locale = null)
    {
        return $this->trans("{$this->getPath()}.fields.{$field}", [], $locale);
    }

    /**
     * 翻译Label.
     *
     * @param  null|string  $label
     * @param  array  $replace
     * @param  string  $locale
     * @return array|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function transLabel(?string $label = null, $replace = [], $locale = null)
    {
        $label = $label ?: admin_controller_name();

        return $this->trans("{$this->getPath()}.labels.{$label}", $replace, $locale);
    }

    /**
     * 翻译.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string  $locale
     * @return false|mixed|string|string[]
     */
    public function trans($key, array $replace = [], $locale = null)
    {
        $method = $this->getTranslateMethod();

        if ($this->translator->has($key)) {
            return $this->translator->$method($key, $replace, $locale);
        }

        if (
            mb_strpos($key, 'global.') !== 0
            && count($arr = explode('.', $key)) > 1
        ) {
            unset($arr[0]);
            array_unshift($arr, 'global');
            $key = implode('.', $arr);

            if (! $this->translator->has($key)) {
                return end($arr);
            }

            return $this->translator->$method($key, $replace, $locale);
        }

        return last(explode('.', $key));
    }

    protected function getTranslateMethod()
    {
        if (static::$method === null) {
            static::$method = version_compare(app()->version(), '6.0', '>=') ? 'get' : 'trans';
        }

        return static::$method;
    }
}
