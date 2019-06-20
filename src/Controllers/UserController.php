<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Models\Repositories\Administrator;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\MiniGrid;
use Dcat\Admin\Show;
use Dcat\Admin\Widgets\Tree;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        if (request('_mini')) {
            return $content->body($this->miniGrid());
        }

        return $content
            ->header(trans('admin.administrator'))
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
            ->header(trans('admin.administrator'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
            ->description(trans('admin.edit'))
            ->body($this->form($id)->edit($id));
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.administrator'))
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
        $grid = new Grid(new Administrator());

        $grid->disableBatchDelete();

        $grid->model()->with('roles');

        $grid->id->bold()->sortable();
        $grid->username;
        $grid->name;
        $grid->roles->pluck('name')->label('primary');

        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');
        $nodes = (new $permissionModel)->allNodes();
        $grid->permissions->display(function ($v, $column) use (&$nodes, $roleModel) {
            if (empty($this->roles)) {
                return;
            }
            return $column->tree(function (Grid\Displayers\Tree $tree) use (&$nodes, $roleModel) {
                $tree->nodes($nodes);

                foreach (array_column($this->roles, 'slug') as $slug) {
                    if ($roleModel::isAdministrator($slug)) {
                        $tree->checkedAll();
                    }
                }
            });
        });

        $grid->created_at;
        $grid->updated_at->sortable();

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->filter(function (Grid\Filter $filter) {
            $filter->equal('id');
            $filter->like('username');
            $filter->like('name');
        });

        return $grid;
    }

    protected function miniGrid()
    {
        $grid = new MiniGrid(new Administrator());

        $grid->id->bold()->sortable();
        $grid->username;
        $grid->name;

        $grid->filter(function (Grid\Filter $filter) {
            $filter->equal('id')->width('270px');
            $filter->like('username')->width('270px');
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
        $show = new Show(new Administrator());

        $show->setId($id);

        $show->id;
        $show->username;
        $show->name;

        $show->newline();

        $show->created_at;
        $show->updated_at;

        $show->divider();

        $show->roles->width(6)->as(function ($roles) {
            return collect($roles)->pluck('name');
        })->label('primary');

        $show->permissions->width(6)->unescape()->as(function () {
            $permissionModel = config('admin.database.permissions_model');
            $roleModel = config('admin.database.roles_model');
            $permissionModel = new $permissionModel;
            $nodes = $permissionModel->allNodes();

            $tree = Tree::make($nodes);

            $isAdministrator = false;
            foreach (array_column($this->roles, 'slug') as $slug) {
                if ($roleModel::isAdministrator($slug)) {
                    $tree->checkedAll();
                    $isAdministrator = true;
                }
            }

            if (!$isAdministrator) {
                $keyName = $permissionModel->getKeyName();
                $tree->checked(
                    $roleModel::getPermissionId(array_column($this->roles, $keyName))->flatten()
                );
            }

            return $tree->render();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form($id = null)
    {
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new Administrator());

        $form->display('id', 'ID');

        if (request()->isMethod('POST')) {
            $userTable = config('admin.database.users_table');
            $userNameRules = "required|unique:{$userTable}";
        } else {
            $userNameRules = 'required';
        }

        $form->text('username', trans('admin.username'))->rules($userNameRules);
        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))
            ->rules('required')
            ->default(function ($form) {
                return $form->model()->get('password');
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))
            ->options($roleModel::all()->pluck('name', 'id'))
            ->customFormat(function ($v) {
                return array_column($v, 'id');
            });

        if ($id) {
            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
        }

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->get('password') != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }

}
