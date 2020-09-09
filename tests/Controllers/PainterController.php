<?php

namespace Tests\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Show;
use Tests\Models\Painter;

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
                $filter->equal('id');
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
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
            $form->display('id', 'ID');

            $form->text('username')->rules('required');
            $form->textarea('bio')->rules('required');

            $form->hasMany('paintings', function (Form\NestedForm $form) {
                $form->text('title');
                $form->textarea('body');
                $form->datetime('completed_at');
            });

            $form->display('created_at', 'Created At');
        });
    }
}
