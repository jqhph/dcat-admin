<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;

class SwitchField extends Field
{
    public function primary()
    {
        return $this->color('var(--primary)');
    }

    public function green()
    {
        return $this->color('var(--success)');
    }

    public function custom()
    {
        return $this->color('var(--custom)');
    }

    public function yellow()
    {
        return $this->color('var(--warning)');
    }

    public function red()
    {
        return $this->color('var(--danger)');
    }

    public function purple()
    {
        return $this->color('var(--purple)');
    }

    public function blue()
    {
        return $this->color('var(--blue)');
    }


    /**
     * Set color of the switcher.
     *
     * @param $color
     * @return $this
     */
    public function color($color)
    {
        return $this->attribute('data-color', $color);
    }

    /**
     *
     * @param $color
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
     * @return int
     */
    protected function prepareToSave($value)
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
        $this->attribute('data-plugin', $this->getFormId().'switchery');

        Admin::script(<<<JS
function swty(){\$('[data-plugin="{$this->getFormId()}switchery"]').each(function(){new Switchery($(this)[0],$(this).data())})} swty();
JS
        );

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('switchery');
    }
}
