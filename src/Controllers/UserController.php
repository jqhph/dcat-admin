<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Auth\Permission;
use Dcat\Admin\Models\Repositories\Administrator;
use Dcat\Admin\Models\Administrator as AdministratorModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\MiniGrid;
use Dcat\Admin\Show;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Tree;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    use HasResourceActions {
        destroy as delete;
    }

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
        $grid->disableCreateButton();

        $grid->model()->with('roles');

        $grid->id('ID')->bold()->sortable();
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
            if ($actions->getKey() == AdministratorModel::DEFAULT_ID) {
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

    /**
     * @return MiniGrid
     */
    protected function miniGrid()
    {
        $grid = new MiniGrid(new Administrator());

        $grid->id->bold()->sortable()->filter(
            Grid\Column\Filter\Equal::make('ID')
        );

        $grid->username->filter(
            Grid\Column\Filter\StartWith::make(__('admin.username'))
        );

        $grid->name->filter(
            Grid\Column\Filter\StartWith::make(__('admin.name'))
        );

        $grid->created_at;

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

        $show->avatar->image();

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

        if ($id == AdministratorModel::DEFAULT_ID) {
            $show->disableDeleteButton();
        }

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form($id = null)
    {
        $userTable = config('admin.database.users_table');

        $connection = config('admin.database.connection');

        $form = new Form(new Administrator());

        $form->display('id', 'ID');

        $form->text('username', trans('admin.username'))
            ->required()
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,$id"]);
        $form->text('name', trans('admin.name'))->required();
        $form->image('avatar', trans('admin.avatar'));

        if ($id) {
            $form->password('password', trans('admin.password'))
                ->rules('confirmed')
                ->customFormat(function ($v) {
                    if ($v == $this->password) {
                        return;
                    }
                    return $v;
                });
            $form->password('password_confirmation', trans('admin.password_confirmation'));
        } else {
            $form->password('password', trans('admin.password'))
                ->required()
                ->rules('confirmed');

            $form->password('password_confirmation', trans('admin.password_confirmation'));
        }

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))
            ->options(function () {
                $roleModel = config('admin.database.roles_model');

                return $roleModel::all()->pluck('name', 'id');
            })
            ->customFormat(function ($v) {
                return array_column($v, 'id');
            });

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->get('password') != $form->password) {
                $form->password = bcrypt($form->password);
            }

            if (! $form->password) {
                $form->deleteInput('password');
            }
        });

        if ($id == AdministratorModel::DEFAULT_ID) {
            $form->disableDeleteButton();
        }

        return $form;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (in_array(AdministratorModel::DEFAULT_ID, Helper::array($id))) {
            Permission::error();
        }

        return $this->delete($id);
    }

}
