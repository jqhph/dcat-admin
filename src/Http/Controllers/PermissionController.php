<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Http\Repositories\Permission;
use Dcat\Admin\Layout\Content;
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
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->body($this->treeView());
    }

    protected function treeView()
    {
        $model = config('admin.database.permissions_model');

        return new Tree(new $model(), function (Tree $tree) {
            $tree->disableCreateButton();
            $tree->disableEditButton();

            $tree->branch(function ($branch) {
                $branchName = htmlspecialchars($branch['name']);
                $branchSlug = htmlspecialchars($branch['slug']);
                $payload = "<div class='pull-left' style='min-width:310px'><b>{$branchName}</b>&nbsp;&nbsp;[<span class='text-primary'>{$branchSlug}</span>]";

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

                $path = collect($path)->map(function ($path) use (&$method) {
                    if (Str::contains($path, ':')) {
                        [$me, $path] = explode(':', $path);

                        $method = array_merge($method, explode(',', $me));
                    }
                    if ($path !== '...' && ! empty(config('admin.route.prefix')) && ! Str::contains($path, '.')) {
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
        $with = [];

        if ($bindMenu = config('admin.menu.permission_bind_menu', true)) {
            $with[] = 'menus';
        }

        return Form::make(Permission::with($with), function (Form $form) use ($bindMenu) {
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

            if ($bindMenu) {
                $form->tree('menus', trans('admin.menu'))
                    ->treeState(false)
                    ->setTitleColumn('title')
                    ->nodes(function () {
                        $model = config('admin.database.menu_model');

                        return (new $model())->allNodes();
                    })
                    ->customFormat(function ($v) {
                        if (! $v) {
                            return [];
                        }

                        return array_column($v, 'id');
                    });
            }

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            $form->disableViewButton();
            $form->disableViewCheck();
        })->saved(function () {
            $model = config('admin.database.menu_model');
            (new $model())->flushCache();
        });
    }

    public function getRoutes()
    {
        $prefix = (string) config('admin.route.prefix');

        $container = collect();

        $routes = collect(app('router')->getRoutes())->map(function ($route) use ($prefix, $container) {
            if (! Str::startsWith($uri = $route->uri(), $prefix) && $prefix && $prefix !== '/') {
                return;
            }

            if (! Str::contains($uri, '{')) {
                if ($prefix !== '/') {
                    $route = Str::replaceFirst($prefix, '', $uri.'*');
                } else {
                    $route = $uri.'*';
                }

                if ($route !== '*') {
                    $container->push($route);
                }
            }

            $path = preg_replace('/{.*}+/', '*', $uri);

            if ($prefix !== '/') {
                return Str::replaceFirst($prefix, '', $path);
            }

            return $path;
        });

        return $container->merge($routes)->filter()->all();
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
