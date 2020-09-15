<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class Slider extends Field
{
    protected $options = [
        'type'     => 'single',
        'prettify' => false,
        'hasGrid'  => true,
    ];

    public function render()
    {
        $this->addVariables(['options' => json_encode($this->options)]);

        return parent::render();
    }
}
