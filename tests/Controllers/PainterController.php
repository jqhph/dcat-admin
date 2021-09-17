<?php

namespace Tests\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Tests\Models\Painter;
use Tests\Models\Painting;

class PainterController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Painter(), function (Grid $grid) {
            $grid->id->sortable();
            $grid->username;
            $grid->bio;
            $grid->created_at;
            $grid->updated_at->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->between('created_at')->datetime();
                $filter->equal('id');
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param  mixed  $id
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Painter(), function (Show $show) {
            $show->id;
            $show->username;
            $show->bio;
            $show->created_at;
            $show->updated_at;

            $show->relation('paintings', function ($model) {
                return Grid::make(Painting::where('painter_id', $model->getKey()), function (Grid $grid) {
                    $grid->column('id')->sortable();
                    $grid->column('title');
                    $grid->column('body');
                    $grid->column('completed_at')->sortable();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(Painter::with('paintings'), function (Form $form) {
            $form->block(6, function (Form\BlockForm $form) {
                $form->showFooter();

                $form->display('id', 'ID');

                $form->text('username')->rules('required');
                $form->textarea('bio')->rules('required');
            });

            $form->block(6, function (Form\BlockForm $form) {
                $form->hasMany('paintings', function (Form\NestedForm $form) {
                    $form->text('title');
                    $form->textarea('body');
                    $form->datetime('completed_at');
                });

                $form->display('created_at', 'Created At');
            });
        });
    }
}
