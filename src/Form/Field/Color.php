<?php

namespace Dcat\Admin\Form\Field;

class Color extends Text
{
    protected static $css = [
        '@admin/dcat/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
    ];

    protected static $js = [
        '@admin/dcat/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js',
    ];

    protected $view = 'admin::form.color';

    /**
     * Use `hex` format.
     *
     * @return $this
     */
    public function hex()
    {
        return $this->options(['format' => 'hex']);
    }

    /**
     * Use `rgb` format.
     *
     * @return $this
     */
    public function rgb()
    {
        return $this->options(['format' => 'rgb']);
    }

    /**
     * Use `rgba` format.
     *
     * @return $this
     */
    public function rgba()
    {
        return $this->options(['format' => 'rgba']);
    }

    protected function addScript()
    {
        $options = json_encode($this->options);

        $this->script = <<<JS
$('{$this->getElementClassSelector()}').colorpicker($options).on('colorpickerChange', function(event) {
    $(this).parents('.input-group').find('.input-group-prepend i').css('background-color', event.color.toString());
});
JS;
    }

    /**
     * Render this filed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->addScript();

        return parent::render();
    }
}
