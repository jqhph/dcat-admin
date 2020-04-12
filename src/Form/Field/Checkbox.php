<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Checkbox as WidgetCheckbox;

class Checkbox extends MultipleSelect
{
    public static $css = [];
    public static $js = [];

    protected $style = 'primary';

    /**
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
     * @param string $style
     *
     * @return $this
     */
    public function style(string $style)
    {
        $this->style = $style;

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

        $checkbox = WidgetCheckbox::make(
            $this->getElementName().'[]',
            $this->options,
            $this->style
        );

        if ($this->attributes['disabled'] ?? false) {
            $checkbox->disable();
        }

        $checkbox->inline()->check(old($this->column, $this->value()));

        $this->addVariables([
            'checkbox' => $checkbox,
        ]);

        $this->script = ';';

        return parent::render();
    }
}
