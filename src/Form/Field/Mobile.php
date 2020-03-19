<?php

namespace Dcat\Admin\Form\Field;

class Mobile extends Text
{
    public static $js = '@jquery.inputmask';
    public static $css = '@jquery.inputmask';

    /**
     * @see https://github.com/RobinHerbots/Inputmask#options
     *
     * @var array
     */
    protected $options = [
        'mask' => '99999999999',
    ];

    public function render()
    {
        $this->inputmask($this->options);

        $this->prepend('<i class="feather icon-smartphone"></i>');

        return parent::render();
    }
}
