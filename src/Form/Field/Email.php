<?php

namespace Dcat\Admin\Form\Field;

class Email extends Text
{
    protected $rules = ['nullable', 'email'];

    public function render()
    {
        $this->prepend('<i class="feather icon-mail"></i>')
            ->type('email');

        return parent::render();
    }
}
