<?php

namespace Dcat\Admin\Form\Field;

class Icon extends Text
{
    public static $js = '@fontawesome-iconpicker';
    public static $css = '@fontawesome-iconpicker';

    public function render()
    {
        $this->script = <<<JS
setTimeout(function () {
    $('{$this->getElementClassSelector()}').iconpicker({placement:'bottomLeft'});
}, 10);
JS;

        $this->defaultAttribute('style', 'width: 200px')
            ->defaultAttribute('autocomplete', 'off');

        return parent::render();
    }
}
