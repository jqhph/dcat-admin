<?php

namespace Tests\Browser\Components\Form\Field;

use Laravel\Dusk\Browser;

class MultipleSelect2 extends Select2
{
    /**
     * 选中下拉选框
     *
     * @param  Browser $browser
     * @param  array   $values
     *
     * @return Browser
     */
    public function choose($browser, $values)
    {
        $values = implode(',', (array) $values);

//dump($browser->resolver->prefix.' || '.$this->formatSelector($browser));

        $browser->script(
            <<<JS
var values = '{$values}';
$('{$this->formatSelector($browser)}').val(values.split(',')).change();
JS
        );

        return $browser;
    }
}
