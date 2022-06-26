<?php

namespace Dcat\Admin\Form\Field;

class Number extends Text
{
    protected $view = 'admin::form.number';

    protected $options = [
        'upClass'   => 'primary shadow-0',
        'downClass' => 'light shadow-0',
        'center'    => true,
        'disabled'  => false,
    ];

    /**
     * Set min value of number field.
     *
     * @param  int  $value
     * @return $this
     */
    public function min($value)
    {
        $this->attribute('min', $value);

        return $this;
    }

    /**
     * Set max value of number field.
     *
     * @param  int  $value
     * @return $this
     */
    public function max($value)
    {
        $this->attribute('max', $value);

        return $this;
    }

    /**
     * Set increment and decrement button to disabled.
     *
     * @param  bool  $value
     * @return $this
     */
    public function disable(bool $value = true)
    {
        parent::disable($value);

        $this->options['disabled'] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareInputValue($value)
    {
        return empty($value) ? 0 : $value;
    }

    /**
     * {@inheritDoc}
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return (int) parent::value();
        }

        return parent::value($value);
    }

    public function render()
    {
        $this->defaultAttribute('style', 'width: 140px;flex:none');

        $this->prepend('');

        return parent::render();
    }
}
