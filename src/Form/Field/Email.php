<?php

namespace Dcat\Admin\Form\Field;

class Email extends Text
{
    protected $rules = ['nullable', 'email'];

    public function render()
    {
        $this->prepend('<i class="ti-email"></i>')
            ->defaultAttribute('type', 'email');

        return parent::render();
    }
}
