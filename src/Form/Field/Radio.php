<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Radio as WidgetRadio;

class Radio extends Field
{
    use CanCascadeFields;

    protected $style = 'primary';

    protected $cascadeEvent = 'change';

    /**
     * @param array|\Closure|string $options
     *
     * @return $this
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

        $this->addCascadeScript();

        $radio = WidgetRadio::make($this->getElementName(), $this->options, $this->style);

        if ($this->attributes['disabled'] ?? false) {
            $radio->disable();
        }

        $radio
            ->inline()
            ->check(old($this->column, $this->value()))
            ->class($this->getElementClassString());

        $this->addVariables([
            'radio' => $radio,
        ]);

        return parent::render();
    }
}
