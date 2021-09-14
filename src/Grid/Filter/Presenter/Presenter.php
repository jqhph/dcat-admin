<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Traits\HasVariables;

abstract class Presenter
{
    use HasVariables;

    /**
     * @var array
     */
    public static $css = [];

    /**
     * @var array
     */
    public static $js = [];

    /**
     * @var AbstractFilter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var int
     */
    protected $width = null;

    /**
     * Set parent filter.
     *
     * @param  AbstractFilter  $filter
     */
    public function setParent(AbstractFilter $filter)
    {
        $this->filter = $filter;

        if ($this->width) {
            $this->width($this->width);
        }
    }

    /**
     * @param  int  $width
     * @return $this
     */
    public function width($width)
    {
        $this->filter->width($width);

        return $this;
    }

    /**
     * 忽略筛选项.
     *
     * @return $this
     */
    public function ignore()
    {
        $this->filter->ignore();

        return $this;
    }

    /**
     * @return string
     */
    public function view(): string
    {
        return $this->view ?: 'admin::filter.'.strtolower(class_basename(static::class));
    }

    /**
     * Set default value for filter.
     *
     * @param $default
     * @return $this
     */
    public function default($default)
    {
        $this->filter->default($default);

        return $this;
    }

    /**
     * Get filter value.
     *
     * @return array|string
     */
    public function value()
    {
        $value = $this->filter->getValue();

        if ($value === null || $value === '') {
            return $this->filter->getDefault();
        }

        return $value;
    }

    /**
     * Collect assets.
     */
    public static function requireAssets()
    {
        if (static::$js) {
            Admin::js(static::$js);
        }
        if (static::$css) {
            Admin::css(static::$css);
        }
    }
}
