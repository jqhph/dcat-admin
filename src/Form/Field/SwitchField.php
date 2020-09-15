<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;

class SwitchField extends Field
{
    public function primary()
    {
        return $this->color(Admin::color()->primary());
    }

    public function green()
    {
        return $this->color(Admin::color()->success());
    }

    public function custom()
    {
        return $this->color(Admin::color()->custom());
    }

    public function yellow()
    {
        return $this->color(Admin::color()->warning());
    }

    public function red()
    {
        return $this->color(Admin::color()->danger());
    }

    public function purple()
    {
        return $this->color(Admin::color()->purple());
    }

    public function blue()
    {
        return $this->color(Admin::color()->blue());
    }

    /**
     * Set color of the switcher.
     *
     * @param string $color
     *
     * @return $this
     */
    public function color($color)
    {
        return $this->attribute('data-color', $color);
    }

    /**
     * @param string $color
     *
     * @return $this
     */
    public function secondary($color)
    {
        return $this->attribute('data-secondary-color', $color);
    }

    /**
     * @return $this
     */
    public function small()
    {
        return $this->attribute('data-size', 'small');
    }

    /**
     * @return $this
     */
    public function large()
    {
        return $this->attribute('data-size', 'large');
    }

    /**
     * @param mixed $value
     *
     * @return int
     */
    protected function prepareInputValue($value)
    {
        return $value ? 1 : 0;
    }

    public function render()
    {
        if (empty($this->attributes['data-size'])) {
            $this->small();
        }
        if (empty($this->attributes['data-color'])) {
            $this->primary();
        }

        $this->attribute('name', $this->getElementName());
        $this->attribute('value', 1);
        $this->attribute('type', 'checkbox');
        $this->attribute('data-plugin', $this->getFormElementId().'switchery');

        return parent::render();
    }
}
