<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class Mobile extends Text
{
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

        $this->prepend('<i class="ti-mobile"></i>')
            ->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('jquery.inputmask');
    }
}
