<?php

namespace Dcat\Admin\Form\Field;

class Number extends Text
{
    protected static $js = [
        '@number-input',
    ];

    /**
     * Width for label and field.
     *
     * @var array
     */
    protected $width = [
        'label' => 4,
        'field' => 2,
    ];

    public function render()
    {
        $this->script = <<<JS
$('{$this->getElementClassSelector()}:not(.initialized)')
    .addClass('initialized')
    .bootstrapNumber({
        upClass: 'success',
        downClass: 'primary',
        center: true
    });
JS;

        $this->prepend('')->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }

    /**
     * Set min value of number field.
     *
     * @param int $value
     *
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
     * @param int $value
     *
     * @return $this
     */
    public function max($value)
    {
        $this->attribute('max', $value);

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return int
     */
    protected function prepareInputValue($value)
    {
        return (int) $value;
    }

    /**
     * Set or get value of the field.
     *
     * @param null $value
     *
     * @return mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return (int) parent::value();
        }

        return parent::value($value);
    }
}
