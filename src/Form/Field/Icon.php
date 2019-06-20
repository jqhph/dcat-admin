<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class Icon extends Text
{
    protected $default = 'fa-pencil';

    public function render()
    {
        $this->script = <<<EOT
$('{$this->getElementClassSelector()}').iconpicker({placement:'bottomLeft'});
EOT;

        $this->prepend('<i class="fa fa-pencil fa-fw"></i>')
            ->defaultAttribute('style', 'width: 200px');

        return parent::render();
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('fontawesome-iconpicker');
    }
}
