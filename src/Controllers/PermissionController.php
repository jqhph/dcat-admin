<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\IFrameGrid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Models\Repositories\Permission;
use Dcat\Admin\Tree;
use Illuminate\Support\Str;

class PermissionController extends AdminController
{
    protected function title()
    {
        return trans('admin.permissions');
    }

    public function index(Content $content)
    {
        if (request(IFrameGrid::QUERY_NAME)) {
            return $content->body($this->iFrameGrid());
        }

        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body($this->treeView());
    }

    protected function iFrameGrid()
    {
        $grid = new IFrameGrid(new Permission());

        $grid->id->sortable();
        $grid->slug;
        $grid->name;

        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('slug');
            $filter->like('name');
        });

        return $grid;
    }

    protected function treeView()
    {
        $model = config('admin.database.permissions_model');

        return new Tree(new $model(), function (Tree $tree) {
            $tree->disableCreateButton();
            $tree->disableEditButton();

            $tree->branch(function ($branch) {
                $payload = "<div class='pull-left' style='min-width:310px'><b>{$branch['name']}</b>&nbsp;&nbsp;[<span class='text-primary'>{$branch['slug']}</span>]";

                $path = array_filter($branch['http_path']);

                if (! $path) {
                    return $payload.'</div>&nbsp;';
                }

                $max = 3;
                if (count($path) > $max) {
                    $path = array_slice($path, 0, $max);
                    array_push($path, '...');
                }

                $method = $branch['http_method'] ?: [];

                $path = collect($path)->map(function ($path) use ($branch, &$method) {
                    if (Str::contains($path, ':')) {
                        [$me, $path] = explode(':', $path);

                        $method = array_merge($method, explode(',', $me));
                    }
                    if ($path !== '...' && ! empty(config('admin.route.prefix'))) {
                        $path = trim(admin_base_path($path), '/');
                    }

                    $color = Admin::color()->primaryDarker();

                    return "<code style='color:{$color}'>$path</code>";
                })->implode('&nbsp;&nbsp;');

                $method = collect($method ?: ['ANY'])->unique()->map(function ($name) {
                    return strtoupper($name);
                })->map(function ($name) {
                    return "<span class='label bg-primary'>{$name}</span>";
                })->implode('&nbsp;').'&nbsp;';

                $payload .= "</div>&nbsp; $method<a class=\"dd-nodrag\">$path</a>";

                return $payload;
            });
        });
    }

    public function form()
    {
        return Form::make(new Permission(), function (Form $form) {
            $permissionTable = config('admin.database.permissions_table');
            $connection = config('admin.database.connection');
            $permissionModel = config('admin.database.permissions_model');

            $id = $form->getKey();

            $form->display('id', 'ID');

            $form->select('parent_id', trans('admin.parent_id'))
                ->options($permissionModel::selectOptions())
                ->saving(function ($v) {
                    return (int) $v;
                });

            $form->text('slug', trans('admin.slug'))
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$permissionTable}"])
                ->updateRules(['required', "unique:{$connection}.{$permissionTable},slug,$id"]);
            $form->text('name', trans('admin.name'))->required();

            $form->multipleSelect('http_method', trans('admin.http.method'))
                ->options($this->getHttpMethodsOptions())
                ->help(trans('admin.all_methods_if_empty'));

            $form->tags('http_path', trans('admin.http.path'))
                ->options($this->getRoutes());

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            $form->disableViewButton();
            $form->disableViewCheck();
        });
    }

    public function getRoutes()
    {
        $prefix = config('admin.route.prefix');

        return collect(app('router')->getRoutes())->map(function ($route) use ($prefix) {
            if (! Str::startsWith($uri = $route->uri(), $prefix)) {
                return;
            }

            return Str::replaceFirst($prefix, '', preg_replace('/{.*}+/', '*', $uri));
        })->filter()->all();
    }

    /**
     * Get options of HTTP methods select field.
     *
     * @return array
     */
    protected function getHttpMethodsOptions()
    {
        $permissionModel = config('admin.database.permissions_model');

        return array_combine($permissionModel::$httpMethods, $permissionModel::$httpMethods);
    }
}
