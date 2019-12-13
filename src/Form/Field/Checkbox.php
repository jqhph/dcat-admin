<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;

class Checkbox extends MultipleSelect
{
    /**
     * @var array
     */
    protected static $css = [];

    /**
     * @var array
     */
    protected static $js = [];

    protected $inline = true;

    protected $circle = false;

    protected $style = 'primary';

    /**
     * Set options.
     *
     * @param array|\Closure|string $options
     *
     * @return $this|mixed
     */
    public function options($options = [])
    {
        if ($options instanceof \Closure) {
            $this->options = $options;

            return $this;
        }

        $this->options = Helper::array($options);

        return $this;
    }

    /**
     * "info", "primary", "inverse", "danger", "success", "purple".
     *
     * @param string $v
     *
     * @return $this
     */
    public function style($v)
    {
        $this->style = $v;

        return $this;
    }

    public function circle(bool $value = true)
    {
        $this->circle = $value;

        return $this;
    }

    /**
     * Draw inline checkboxes.
     *
     * @return $this
     */
    public function inline()
    {
        $this->inline = true;

        return $this;
    }

    /**
     * Draw stacked checkboxes.
     *
     * @return $this
     */
    public function stacked()
    {
        $this->inline = false;

        return $this;
    }

    /**
     * Set or get value of the field.
     *
     * @param null $value
     *
     * @return mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            if ($this->value === null) {
                return $this->default();
            }

            if (count($this->value) === 0) {
                return $this->default();
            }

            return $this->value;
        }

        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if ($this->options instanceof \Closure) {
            $this->options(
                $this->options->call($this->values(), $this->value(), $this)
            );
        }

        $this->addVariables([
            'inline'        => $this->inline,
            'checkboxStyle' => 'primary',
            'circle'        => $this->circle,
        ]);

        $this->script = ';';

        return parent::render();
    }
}
