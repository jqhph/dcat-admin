<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Models\Repositories\Menu;
use Dcat\Admin\Form;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Tree;
use Dcat\Admin\Widgets\Box;
use Illuminate\Routing\Controller;

class MenuController extends Controller
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
            ->header(trans('admin.menu'))
            ->description(trans('admin.list'))
            ->row(function (Row $row) {
                $row->column(7, $this->treeView()->render());

                $row->column(5, function (Column $column) {
                    $form = new \Dcat\Admin\Widgets\Form();
                    $form->action(admin_base_path('auth/menu'));

                    $menuModel = config('admin.database.menu_model');
                    $permissionModel = config('admin.database.permissions_model');
                    $roleModel = config('admin.database.roles_model');

                    $form->select('parent_id', trans('admin.parent_id'))->options($menuModel::selectOptions());
                    $form->text('title', trans('admin.title'))->rules('required');
                    $form->icon('icon', trans('admin.icon'))->default('fa-bars')->rules('required')->help($this->iconHelp());
                    $form->text('uri', trans('admin.uri'));
                    $form->select('roles', trans('admin.roles'))
                        ->options($roleModel::all()->pluck('name', 'id'));
                    if ($menuModel::withPermission()) {
                        $form->select('permission_id', trans('admin.permission'))->options($permissionModel::selectOptions());
                    }
                    $form->hidden('_token')->default(csrf_token());

                    $form->setWidth(9, 2);

                    $column->append(Box::make(trans('admin.new'), $form)->style('success'));
                });
            });
    }

    /**
     * @return \Dcat\Admin\Tree
     */
    protected function treeView()
    {
        $menuModel = config('admin.database.menu_model');

        $tree = new Tree(new $menuModel);

        if ($menuModel::withPermission()) {
            $tree->query(function ($model) {
                return $model->with('permission');
            });
        }
        $tree->disableCreateButton();
        $tree->disableQuickCreateButton();

        $roleText = trans('admin.roles');
        $permissionText = trans('admin.permissions');

        $tree->branch(function ($branch) use ($roleText, $permissionText) {
            $payload = "<i class='fa {$branch['icon']}'></i>&nbsp;<strong>{$branch['title']}</strong>";

            if (!isset($branch['children'])) {
                if (url()->isValidUrl($branch['uri'])) {
                    $uri = $branch['uri'];
                } else {
                    $uri = admin_base_path($branch['uri']);
                }

                $payload .= "&nbsp;&nbsp;&nbsp;<a href=\"$uri\" class=\"dd-nodrag\">$uri</a>";
            }

            if (!empty($branch['roles'])) {
                $roles = collect($branch['roles'])
                    ->pluck('name')
                    ->join('&nbsp;');

                $slug = current($branch['roles'])['slug'] ?? '';
                $payload .= str_repeat('&nbsp;', 6)."<span title='$slug'>[{$roleText}: $roles]</span>";
            }

            if (!empty($branch['permission'])) {

                $payload .= str_repeat('&nbsp;', empty($branch['roles']) ? 6 : 3)
                    . "<span>[{$permissionText}: {$branch['permission']['slug']}]</span>";
            }

            return $payload;
        });

        return $tree;
    }

    /**
     * Edit interface.
     *
     * @param string  $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.menu'))
            ->description(trans('admin.edit'))
            ->row($this->form()->edit($id));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $menuModel = config('admin.database.menu_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new Menu());

        $form->tools(function (Form\Tools $tools) {
            $tools->disableView();
        });

        $form->display('id', 'ID');

        $form->select('parent_id', trans('admin.parent_id'))->options($menuModel::selectOptions());
        $form->text('title', trans('admin.title'))->rules('required');
        $form->icon('icon', trans('admin.icon'))->default('fa-bars')->rules('required')->help($this->iconHelp());
        $form->text('uri', trans('admin.uri'));
        $form->select('roles', trans('admin.roles'))
            ->options($roleModel::all()->pluck('name', 'id'))
            ->customFormat(function ($v) {
                return join(',', array_column($v, 'id'));
            });
        if ($menuModel::withPermission()) {
            $form->select('permission_id', trans('admin.permission'))->options($permissionModel::selectOptions());
        }

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        return $form;
    }

    /**
     * Help message for icon field.
     *
     * @return string
     */
    protected function iconHelp()
    {
        return 'For more icons please see <a href="http://fontawesome.io/icons/" target="_blank">http://fontawesome.io/icons/</a>';
    }
}
