<?php

namespace Dcat\Admin\Form\Field;

class Datetime extends Date
{
    protected $format = 'yyyy-mm-dd HH:mm:ss';

    public function render()
    {
        $this->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }
}
