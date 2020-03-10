<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;

class MultipleSelect2 extends Select2
{
    /**
     * 选中下拉选框
     *
     * @param  Browser $browser
     * @param  array   $values
     * @param  int  $day
     * @return void
     */
    public function choose($browser, $values)
    {
        $values = implode(',', (array) $values);

        $browser->script(
            <<<JS
var values = '{$values}';
$('{$this->selector()}').val(values.split(',')).change();
JS
        );
    }
}
