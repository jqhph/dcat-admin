<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RouteController extends Controller
{
    public function index()
    {
        $content = new Content();

        $content->title(trans('admin.routes'))->description(trans('admin.list'));

        $content->body(function (Row $row) {
            $colors = [
                'GET'    => 'primary',
                'HEAD'   => 'gray',
                'POST'   => 'green',
                'PUT'    => 'yellow',
                'DELETE' => 'red',
                'PATCH'  => 'purple',
                'OPTIONS'=> 'blue',
            ];

            $grid = new Grid();

            $grid->model()->setData(function (Grid\Model $model) {
                return $this->getModel()->setRoutes($this->getRoutes())->get($model);
            });

            $grid->number();

            $grid->method(trans('admin.method'))->map(function ($method) use ($colors) {
                return "<span class=\"label bg-{$colors[$method]}\">$method</span>";
            })->implode('&nbsp;');

            $grid->uri(trans('admin.uri'))->sortable()->display(function ($v) {
                return "<code>$v</code>";
            });

            $grid->name(trans('admin.alias'))->bold();

            $grid->action(trans('admin.route_action'))->display(function ($uri) {
                if ($uri === 'Closure') {
                    return "<a class='badge bg-gray'>$uri</a>";
                }

                return preg_replace('/@.+/', '<code>$0</code>', $uri);
            });
            $grid->middleware(trans('admin.middleware'))->badge('gray');

            $grid->disablePagination();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExporter();

            $grid->filter(function (Grid\Filter $filter) use ($colors) {
                $values = array_keys($colors);

                $filter->equal('method', trans('admin.method'))->select(array_combine($values, $values));
                $filter->equal('uri', trans('admin.uri'));
                $filter->equal('action', trans('admin.route_action'));
            });

            $row->column(12, $grid);
        });

        return $content;
    }

    protected function getModel()
    {
        return new class() {
            protected $routes;

            protected $where = [];

            public function setRoutes($routes)
            {
                $this->routes = $routes;

                return $this;
            }

            public function where($column, $condition)
            {
                $this->where[$column] = trim($condition);

                return $this;
            }

            public function orderBy()
            {
                return $this;
            }

            public function get(Grid\Model $model)
            {
                $model->getQueries()->unique()->each(function ($query) use (&$eloquent) {
                    if ($query['method'] == 'paginate' || $query['method'] == 'get') {
                        return;
                    }

                    call_user_func_array([$this, $query['method']], $query['arguments'] ?? []);
                });

                return $this->routes = collect($this->routes)->filter(function ($route) {
                    foreach ($this->where as $column => $condition) {
                        if (is_array($route[$column])) {
                            if (! in_array($condition, $route[$column])) {
                                return false;
                            }
                        } elseif (! Str::contains(strtolower($route[$column]), strtolower($condition))) {
                            return false;
                        }
                    }

                    return true;
                });
            }
        };
    }

    public function getRoutes()
    {
        $routes = collect(\Route::getRoutes())->map(function ($route) {
            return $this->getRouteInformation($route);
        })->all();

        if ($sort = request('_sort')) {
            $routes = $this->sortRoutes($sort, $routes);
        }

        return array_filter($routes);
    }

    /**
     * Get the route information for a given route.
     *
     * @param \Illuminate\Routing\Route $route
     *
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        try {
            return [
                'host'       => $route->domain(),
                'method'     => $route->methods(),
                'uri'        => $route->uri(),
                'name'       => $route->getName(),
                'action'     => $route->getActionName(),
                'middleware' => $this->getRouteMiddleware($route),
            ];
        } catch (\Exception $e) {
            return [
                'host'       => $route->domain(),
                'method'     => $route->methods(),
                'uri'        => $route->uri(),
                'name'       => $route->getName(),
                'action'     => '<span class="label label-danger">Undefined</span>',
                'middleware' => $this->getRouteMiddleware($route),
            ];
        }
    }

    /**
     * Sort the routes by a given element.
     *
     * @param string $sort
     * @param array  $routes
     *
     * @return array
     */
    protected function sortRoutes($sort, $routes)
    {
        return Arr::sort($routes, function ($route) use ($sort) {
            $column = $sort['column'];
            $type = $sort['type'];

            return $type === 'asc' ? ! $route[$column] : $route[$column];
        });
    }

    /**
     * Get before filters.
     *
     * @param \Illuminate\Routing\Route $route
     *
     * @return string
     */
    protected function getRouteMiddleware($route)
    {
        return collect($route->gatherMiddleware())->map(function ($middleware) {
            return $middleware instanceof \Closure ? 'Closure' : $middleware;
        });
    }
}
