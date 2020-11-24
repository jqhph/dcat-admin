<?php

namespace Dcat\Admin\Form\Field;

class Mac extends Text
{
    public static $js = '@jquery.inputmask';
    public static $css = '@jquery.inputmask';

    protected $rules = ['nullable', 'mac'];

    /**
     * @see https://github.com/RobinHerbots/Inputmask#options
     *
     * @var array
     */
    protected $options = [
        'alias' => 'mac',
    ];

    public function render()
    {
        $this->inputmask($this->options);

        $this->defaultAttribute('style', 'width: 160px;flex:none');

        $this->prepend('<i class="fa fa-desktop fa-fw"></i>')
            ->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }
}
