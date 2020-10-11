<?php

namespace Dcat\Admin\Http\Actions\Extensions;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class Disable extends RowAction
{
    public function title()
    {
        return trans('admin.disable');
    }

    public function handle()
    {
        Admin::extension()->enable($this->getKey(), false);

        return $this
            ->response()
            ->success(trans('admin.update_succeeded'))
            ->refresh();
    }
}
