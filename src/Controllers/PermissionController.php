<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Models\Repositories\Permission;
use Dcat\Admin\Show;
use Dcat\Admin\SimpleGrid;
use Dcat\Admin\Tree;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class PermissionController extends Controller
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
        if (request(SimpleGrid::QUERY_NAME)) {
            return $content->body($this->simpleGrid());
        }

        return $content
            ->title(trans('admin.permissions'))
            ->description(trans('admin.list'))
            ->body(function (Row $row) {
                if (request('_layout')) {
                    $row->column(12, $this->grid());
                } else {
                    $row->column(12, $this->treeView());
                }
            });
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
            ->title(trans('admin.permissions'))
            ->description(trans('admin.detail'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title(trans('admin.permissions'))
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
            ->title(trans('admin.permissions'))
            ->description(trans('admin.create'))
            ->body($this->form());
    }

    protected function simpleGrid()
    {
        $grid = new SimpleGrid(new Permission());

        $grid->id->bold()->sortable();
        $grid->slug;
        $grid->name;

        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('slug');
            $filter->like('name');
        });

        return $grid;
    }

    /**
     * @return \Dcat\Admin\Tree
     */
    protected function treeView()
    {
        $model = config('admin.database.permissions_model');

        $tree = new Tree(new $model());

        $tree->disableCreateButton();

        $tree->tools(function (Tree\Tools $tools) {
            $label = trans('admin.table');
            $url = url(request()->getPathInfo()).'?_layout=1';
            $tools->add("<a class='btn btn-sm btn-default' href='{$url}'>$label</a>");
        });

        $tree->branch(function ($branch) {
            $payload = "<div class='pull-left' style='min-width:310px'><b>{$branch['name']}</b>&nbsp;&nbsp;[<span class='text-blue'>{$branch['slug']}</span>]";

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
                    $path = admin_base_path($path);
                }

                return "<code>$path</code>";
            })->implode('&nbsp;&nbsp;');

            $method = collect($method ?: ['ANY'])->unique()->map(function ($name) {
                return strtoupper($name);
            })->map(function ($name) {
                return "<span class='label label-primary'>{$name}</span>";
            })->implode('&nbsp;').'&nbsp;';

            $payload .= "</div>&nbsp; $method<a class=\"dd-nodrag\">$path</a>";

            return $payload;
        });

        return $tree;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Permission());

        $grid->id('ID')->bold()->sortable();
        $grid->slug->label('primary');
        $grid->name;

        $grid->http_path->display(function ($path) {
            if (! $path) {
                return;
            }

            $method = $this->http_method ?: ['ANY'];
            $method = collect($method)->map(function ($name) {
                return strtoupper($name);
            })->map(function ($name) {
                return "<span class='label label-primary'>{$name}</span>";
            })->implode('&nbsp;').'&nbsp;';

            return collect($path)->filter()->map(function ($path) use ($method) {
                if (Str::contains($path, ':')) {
                    [$method, $path] = explode(':', $path);
                    $method = collect(explode(',', $method))->map(function ($name) {
                        return strtoupper($name);
                    })->map(function ($name) {
                        return "<span class='label label-primary'>{$name}</span>";
                    })->implode('&nbsp;').'&nbsp;';
                }

                if (! empty(config('admin.route.prefix'))) {
                    $path = admin_base_path($path);
                }

                return "<div style='margin-bottom: 5px;'>$method<code>$path</code></div>";
            })->implode('');
        });

        $grid->created_at;
        $grid->updated_at->sortable();

        $grid->disableEditButton();
        $grid->showQuickEditButton();
        $grid->enableDialogCreate();

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });

            $label = trans('admin.default');
            $url = url(request()->getPathInfo());
            $tools->append("<a class='btn btn-sm btn-default' href='{$url}'>$label</a>");
        });

        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('slug');
            $filter->like('name');
            $filter->like('http_path');
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
        $show = new Show($id, new Permission());

        $show->id;
        $show->slug;
        $show->name;

        $show->http_path->unescape()->as(function ($path) {
            return collect($path)->filter()->map(function ($path) {
                $method = $this->http_method ?: ['ANY'];

                if (Str::contains($path, ':')) {
                    [$method, $path] = explode(':', $path);
                    $method = explode(',', $method);
                }

                $method = collect($method)->map(function ($name) {
                    return strtoupper($name);
                })->map(function ($name) {
                    return "<span class='label label-primary'>{$name}</span>";
                })->implode('&nbsp;');

                if (! empty(config('admin.route.prefix'))) {
                    $path = '/'.trim(config('admin.route.prefix'), '/').$path;
                }

                return "<div style='margin-bottom: 5px;'>$method<code>$path</code></div>";
            })->implode('');
        });

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
        return Form::make(new Permission(), function (Form $form) {
            $permissionTable = config('admin.database.permissions_table');
            $connection = config('admin.database.connection');

            $id = $form->key();

            $form->display('id', 'ID');

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

    /**
     * @return array
     */
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
