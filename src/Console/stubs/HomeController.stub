<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Dcat\Admin\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Box;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(
                    4,
                    Box::make('Environment', Dashboard::environment())
                        ->padding('0')
                        ->style('default')
                        ->collapsable()
                );

                $row->column(
                    4,
                    Box::make('Dependencies', Dashboard::dependencies())
                        ->padding('0')
                        ->style('default')
                        ->collapsable()
                );

                $row->column(
                    4,
                    Box::make('Extensions', Dashboard::extensions())
                        ->padding('0')
                        ->style('default')
                        ->collapsable()
                );
            });
    }
}
