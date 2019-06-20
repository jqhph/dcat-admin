<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Illuminate\Contracts\Support\Arrayable;

class Radio extends Field
{
    protected $inline = true;

    protected $style = 'primary';

    /**
     * Set options.
     *
     * @param array|callable|string $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if (is_callable($options)) {
            $this->options = $options;

            return $this;
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = (array) $options;

        return $this;
    }

    /**
     * Draw inline radios.
     *
     * @return $this
     */
    public function inline()
    {
        $this->inline = true;

        return $this;
    }

    /**
     * Draw stacked radios.
     *
     * @return $this
     */
    public function stacked()
    {
        $this->inline = false;

        return $this;
    }

    /**
     * "info", "primary", "inverse", "danger", "success", "purple"
     *
     * @param $v
     * @return $this
     */
    public function style($v)
    {
        $this->style = $v;

        return $this;
    }

    /**
     * Set options.
     *
     * @param array|callable|string $values
     *
     * @return $this
     */
    public function values($values)
    {
        return $this->options($values);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if (is_callable($this->options)) {
            $this->options = $this->options->bindTo($this->getFormModel());

            $this->options(call_user_func($this->options, $this->value, $this));
        }

        $this->addVariables([
            'options' => $this->options,
            'inline' => $this->inline,
            'radioStyle' => $this->style,
        ]);

        return parent::render();
    }
}
