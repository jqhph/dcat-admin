<?php

namespace Tests\Controllers;

use App\Http\Controllers\Controller;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Tests\Repositories\Report;

class ReportController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('报表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Report());

        // 开启responsive插件
        $grid->responsive();

        $grid->disableActions();
        $grid->disableBatchDelete();
        $grid->disableCreateButton();

        $grid->rowSelector()->style('success')->click();

        $grid->combine('avgCost', ['avgMonthCost', 'avgQuarterCost', 'avgYearCost'])->responsive()->help('test');
        $grid->combine('avgVist', ['avgMonthVist', 'avgQuarterVist', 'avgYearVist'])->responsive();
        $grid->combine('top', ['topCost', 'topVist', 'topIncr'])->responsive()->style('color:#1867c0');

        $grid->content->limit(50)->responsive();
        $grid->cost->sortable()->responsive();
        $grid->avgMonthCost->responsive();
        $grid->avgQuarterCost->responsive()->setHeaderAttributes(['style' => 'color:#5b69bc']);
        $grid->avgYearCost->responsive();
        $grid->avgMonthVist->responsive();
        $grid->avgQuarterVist->responsive();
        $grid->avgYearVist->responsive();
        $grid->incrs->hide();
        $grid->avgVists->hide();
        $grid->topCost->responsive();
        $grid->topVist->responsive();
        $grid->topIncr->responsive();
        $grid->date->sortable()->responsive();

        $grid->filter(function (Grid\Filter $filter) {
            $filter->scope(1, admin_trans_field('month'))->where('date', 2019, '<=');
            $filter->scope(2, admin_trans_label('quarter'))->where('date', 2019, '<=');
            $filter->scope(3, admin_trans_label('year'))->where('date', 2019, '<=');

            $filter->equal('content');
            $filter->equal('cost');
        });

        return $grid;
    }
}
