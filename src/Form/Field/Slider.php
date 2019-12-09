<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class Slider extends Field
{
    protected static $css = [
        '/vendor/dcat-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.css',
        '/vendor/dcat-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.skinNice.css',
    ];

    protected static $js = [
        '/vendor/dcat-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.min.js',
    ];

    protected $options = [
        'type'     => 'single',
        'prettify' => false,
        'hasGrid'  => true,
    ];

    public function render()
    {
        $option = json_encode($this->options);

        $this->script = "setTimeout(function () { $('{$this->elementClassSelector()}').ionRangeSlider($option) }, 400);";

        return parent::render();
    }
}
