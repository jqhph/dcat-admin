<?php

namespace Dcat\Admin\Form\Field;

class Rate extends Text
{
    public function render()
    {
        $this->prepend('%')
            ->type('number')
            ->defaultAttribute('placeholder', 0);

        return parent::render();
    }
}
