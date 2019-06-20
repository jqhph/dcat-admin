<?php

namespace Dcat\Admin\Form\Field;

class Password extends Text
{
    public function render()
    {
        $this->prepend('<i class="ti-eye"></i>')
            ->defaultAttribute('type', 'password');

        return parent::render();
    }
}
