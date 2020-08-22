<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Support\Arr;

class Dashboard
{
    /**
     * @return mixed
     */
    public static function title()
    {
        return view('admin::dashboard.title');
    }
}
