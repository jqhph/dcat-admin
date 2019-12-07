<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class Currency extends Text
{
    /**
     * @var string
     */
    protected $symbol = '$';

    /**
     * @see https://github.com/RobinHerbots/Inputmask#options
     *
     * @var array
     */
    protected $options = [
        'alias'              => 'currency',
        'radixPoint'         => '.',
        'prefix'             => '',
        'removeMaskOnSubmit' => true,
    ];

    /**
     * Set symbol for currency field.
     *
     * @param string $symbol
     *
     * @return $this
     */
    public function symbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Set digits for input number.
     *
     * @param int $digits
     *
     * @return $this
     */
    public function digits($digits)
    {
        return $this->options(compact('digits'));
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareToSave($value)
    {
        return (float) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->inputmask($this->options);

        $this->prepend($this->symbol)
            ->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('jquery.inputmask');
    }
}
