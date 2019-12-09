<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Widgets\Color;

class SwitchField extends Field
{
    public function primary()
    {
        return $this->color(Color::primary());
    }

    public function green()
    {
        return $this->color(Color::success());
    }

    public function custom()
    {
        return $this->color(Color::custom());
    }

    public function yellow()
    {
        return $this->color(Color::warning());
    }

    public function red()
    {
        return $this->color(Color::danger());
    }

    public function purple()
    {
        return $this->color(Color::purple());
    }

    public function blue()
    {
        return $this->color(Color::blue());
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

        $this->attribute('name', $this->elementName());
        $this->attribute('value', 1);
        $this->attribute('type', 'checkbox');
        $this->attribute('data-plugin', $this->formElementId().'switchery');

        Admin::script(
            <<<JS
function swty(){\$('[data-plugin="{$this->formElementId()}switchery"]').each(function(){new Switchery($(this)[0],$(this).data())})} swty();
JS
        );

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('switchery');
    }
}
