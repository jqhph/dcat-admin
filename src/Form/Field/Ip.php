<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class Ip extends Text
{
    protected $rules = ['nullable', 'ip'];

    /**
     * @see https://github.com/RobinHerbots/Inputmask#options
     *
     * @var array
     */
    protected $options = [
        'alias' => 'ip',
    ];

    public static function collectAssets()
    {
        Admin::collectComponentAssets('jquery.inputmask');
    }

    public function render()
    {
        $this->inputmask($this->options);

        $this->prepend('<i class="fa fa-laptop fa-fw"></i>')
            ->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }
}
