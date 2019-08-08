<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Models\Repositories\Role;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\MiniGrid;
use Dcat\Admin\Show;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header(trans('admin.roles'))
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.roles'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.roles'))
            ->description(trans('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.roles'))
            ->description(trans('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        if ($mini = request('_mini')) {
            $grid = new MiniGrid(new Role());
        } else {
            $grid = new Grid(new Role());
        }

        $grid->disableBatchDelete();
        $grid->disableCreateButton();

        $grid->id->bold()->sortable();
        $grid->slug->label('primary');
        $grid->name;

        if (!$mini) {
            $grid->created_at;
            $grid->updated_at->sortable();
        }

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $roleModel = config('admin.database.roles_model');
            if ($roleModel::isAdministrator($actions->row->slug)) {
                $actions->disableDelete();
            }
        });

        $grid->filter(function (Grid\Filter $filter) {
            $filter->equal('id')->width('270px');
            $filter->like('slug')->width('270px');
            $filter->like('name')->width('270px');
        });

        return $grid;
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
        $show = new Show(new Role());

        $show->setId($id);

        $show->id;
        $show->slug;
        $show->name;

        $show->permissions->width(12)->as(function ($permission) {
            return collect($permission)->pluck('name');
        })->label('primary');

        $show->divider();

        $show->created_at;
        $show->updated_at;

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $form = new Form(new Role());

        $form->display('id', 'ID');

        $form->text('slug', trans('admin.slug'))->required()->customPrepare(function ($value) {
            return $value;
        });
        $form->text('name', trans('admin.name'))->required();

        $permissionModel = config('admin.database.permissions_model');
        $permissionModel = new $permissionModel;
        $form->tree('permissions')
            ->nodes($permissionModel->allNodes())
            ->customFormat(function ($v) {
                if (!$v) return [];

                return array_column($v, 'id');
            });

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        return $form;
    }
}
