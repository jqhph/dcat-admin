<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class Slider extends Field
{
    protected static $css = [
        '@ionslider',
    ];

    protected static $js = [
        '@ionslider',
    ];

    protected $options = [
        'type'     => 'single',
        'prettify' => false,
        'hasGrid'  => true,
    ];

    public function render()
    {
        $option = json_encode($this->options);

        $this->script = "setTimeout(function () { $('{$this->getElementClassSelector()}').ionRangeSlider($option) }, 400);";

        return parent::render();
    }
}
