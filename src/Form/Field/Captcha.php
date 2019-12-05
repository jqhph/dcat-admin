<?php

namespace Dcat\Admin\Form\Field;

class Captcha extends Text
{
    protected $rules = ['required', 'captcha'];

    protected $view = 'admin::form.captcha';

    public function __construct()
    {
        if (!class_exists(\Mews\Captcha\Captcha::class)) {
            throw new \Exception('To use captcha field, please install [mews/captcha] first.');
        }

        $this->column = '__captcha__';
        $this->label  = trans('admin.captcha');
    }

    public function setForm($form = null)
    {
        parent::setForm($form);

        if (method_exists($this->form, 'ignore')) {
            $this->form->ignore($this->column);
        }

        return $this;
    }

    public function render()
    {
        $this->script = <<<JS
$('#{$this->column}-captcha').click(function () {
    $(this).attr('src', $(this).attr('src')+'?'+Math.random());
});
JS;

        return parent::render();
    }
}
