<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Input extends Editable
{
    protected $type = 'input';

    protected $view = 'admin::grid.displayer.editinline.input';

    public function display($options = [])
    {
        if (! empty($options['mask'])) {
            Admin::requireAssets('@jquery.inputmask');
        }

        return parent::display($options);
    }
}
