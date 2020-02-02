<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Routing\Controller;

class IconController extends Controller
{
    public function index(Content $content)
    {
        Admin::style('.icon-list-demo div {
            cursor: pointer;
            line-height: 45px;
            white-space: nowrap;
            color: #75798B;
        }.icon-list-demo i {
            display: inline-block;
            font-size: 18px;
            margin: 0;
            vertical-align: middle;
            width: 40px;
        }');

        return $content->title('Icons')->description(' ')->body(function (Row $row) {
            $tab = Tab::make()->padding('20px')->custom();

            $tab->add('Themify', view('admin::helpers.themify'));
//            $tab->add('da-box Design', view('admin::helpers.da-box'));
            $tab->add('Font Awesome', view('admin::helpers.font-awesome'));
            $tab->add('Glyphicons', view('admin::helpers.glyphicons'));

            $row->column(12, $tab);
        });
    }
}
