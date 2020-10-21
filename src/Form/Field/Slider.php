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
}
