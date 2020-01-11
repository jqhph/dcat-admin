<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Auth\Permission;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Models\Administrator as AdministratorModel;
use Dcat\Admin\Models\Repositories\Administrator;
use Dcat\Admin\Show;
use Dcat\Admin\SimpleGrid;
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
        if (request(SimpleGrid::QUERY_NAME)) {
            return $content->body($this->simpleGrid());
        }

        return $content
            ->title(trans('admin.administrator'))
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
            ->title(trans('admin.administrator'))
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
            ->title(trans('admin.administrator'))
            ->description(trans('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->title(trans('admin.administrator'))
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
        return Grid::make(new Administrator('roles'), function (Grid $grid) {
            $grid->id('ID')->bold()->sortable();
            $grid->username;
            $grid->name;
            $grid->roles->pluck('name')->label('primary');

            $permissionModel = config('admin.database.permissions_model');
            $roleModel = config('admin.database.roles_model');
            $nodes = (new $permissionModel())->allNodes();
            $grid->permissions
                ->if(function () {
                    return ! empty($this->roles);
                })
                ->tree(function (Grid\Displayers\Tree $tree) use (&$nodes, $roleModel) {
                    $tree->nodes($nodes);

                    foreach (array_column($this->roles, 'slug') as $slug) {
                        if ($roleModel::isAdministrator($slug)) {
                            $tree->checkedAll();
                        }
                    }
                })
                ->else()
                ->showEmpty();

            $grid->created_at;
            $grid->updated_at->sortable();

            $grid->disableBatchDelete();
            $grid->showQuickEditButton();
            $grid->disableFilterButton();
            $grid->quickSearch(['id', 'name', 'username']);
            $grid->createMode(Grid::CREATE_MODE_DIALOG);

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->key() == AdministratorModel::DEFAULT_ID) {
                    $actions->disableDelete();
                }
            });
        });
    }

    /**
     * @return SimpleGrid
     */
    protected function simpleGrid()
    {
        $grid = new SimpleGrid(new Administrator());

        $grid->quickSearch(['id', 'name', 'username']);

        $grid->id->bold()->sortable();
        $grid->username;
        $grid->name;
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
        return Show::make($id, new Administrator('roles'), function (Show $show) {
            $show->id;
            $show->username;
            $show->name;

            $show->avatar->image();

            $show->newline();

            $show->created_at;
            $show->updated_at;

            $show->divider();

            $show->roles->width(6)->as(function ($roles) {
                if (! $roles) {
                    return;
                }

                return collect($roles)->pluck('name');
            })->label('primary');

            $show->permissions->width(6)->unescape()->as(function () {
                $roles = (array) $this->roles;

                $permissionModel = config('admin.database.permissions_model');
                $roleModel = config('admin.database.roles_model');
                $permissionModel = new $permissionModel();
                $nodes = $permissionModel->allNodes();

                $tree = Tree::make($nodes);

                $isAdministrator = false;
                foreach (array_column($roles, 'slug') as $slug) {
                    if ($roleModel::isAdministrator($slug)) {
                        $tree->checkedAll();
                        $isAdministrator = true;
                    }
                }

                if (! $isAdministrator) {
                    $keyName = $permissionModel->getKeyName();
                    $tree->checked(
                        $roleModel::getPermissionId(array_column($roles, $keyName))->flatten()
                    );
                }

                return $tree->render();
            });

            if ($show->key() == AdministratorModel::DEFAULT_ID) {
                $show->disableDeleteButton();
            }
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Form::make(new Administrator('roles'), function (Form $form) {
            $userTable = config('admin.database.users_table');

            $connection = config('admin.database.connection');

            $id = $form->key();

            $form->display('id', 'ID');

            $form->text('username', trans('admin.username'))
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$userTable}"])
                ->updateRules(['required', "unique:{$connection}.{$userTable},username,$id"]);
            $form->text('name', trans('admin.name'))->required();
            $form->image('avatar', trans('admin.avatar'));

            if ($id) {
                $form->password('password', trans('admin.password'))
                    ->minLength(5)
                    ->maxLength(20)
                    ->customFormat(function ($v) {
                        if ($v == $this->password) {
                            return;
                        }

                        return $v;
                    });
            } else {
                $form->password('password', trans('admin.password'))
                    ->required()
                    ->minLength(5)
                    ->maxLength(20);
            }

            $form->password('password_confirmation', trans('admin.password_confirmation'))->same('password');

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

            if ($id == AdministratorModel::DEFAULT_ID) {
                $form->disableDeleteButton();
            }
        })->saving(function (Form $form) {
            if ($form->password && $form->model()->get('password') != $form->password) {
                $form->password = bcrypt($form->password);
            }

            if (! $form->password) {
                $form->deleteInput('password');
            }
        });
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
