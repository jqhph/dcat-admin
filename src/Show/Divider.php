<?php

namespace Dcat\Admin\Show;

class Divider extends Field
{
    public function render()
    {
        return '<div class="col-sm-12"><hr style="margin-top:15px;"/></div>';
    }
}
