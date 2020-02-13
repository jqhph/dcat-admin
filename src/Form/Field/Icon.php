<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class Icon extends Text
{
    public function render()
    {
        $this->script = <<<JS
setTimeout(function () {
    $('{$this->elementClassSelector()}').iconpicker({placement:'bottomLeft'});
}, 10);
JS;

        $this->defaultAttribute('style', 'width: 200px')
            ->defaultAttribute('autocomplete', 'off');

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('fontawesome-iconpicker');
    }
}
