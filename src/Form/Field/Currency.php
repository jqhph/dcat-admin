<?php

namespace Dcat\Admin\Form\Field;

class Currency extends Text
{
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
        'rightAlign'         => false,
    ];

    /**
     * Set symbol for currency field.
     *
     * @param  string  $symbol
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
     * @param  int  $digits
     * @return $this
     */
    public function digits($digits)
    {
        return $this->mergeOptions(compact('digits'));
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    protected function prepareInputValue($value)
    {
        return is_string($value) ? str_replace(',', '', $value) : $value;
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
}
