<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class Icon extends Text
{
    protected $default = null;

    public function render()
    {
        $this->script = <<<JS
setTimeout(function () {
    $('{$this->getElementClassSelector()}').iconpicker({placement:'bottomLeft'});
}, 10);
JS;
        if (is_null($this->default)) {
            $this->default('');
        }
 
        $this->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('fontawesome-iconpicker');
    }
}
