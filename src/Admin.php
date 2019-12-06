<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Controllers\AuthController;
use Dcat\Admin\Exception\Handler;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Menu;
use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Layout\SectionManager;
use Dcat\Admin\Models\HasPermissions;
use Dcat\Admin\Repositories\Proxy;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasAssets;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

/**
 * Class Admin.
 */
class Admin
{
    use HasAssets;

    /**
     * The Easy admin version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * @var array
     */
    protected static $extensions = [];

    /**
     * @var array
     */
    protected static $availableExtensions;

    /**
     * @var string
     */
    protected static $metaTitle;

    /**
     * @var string
     */
    protected static $favicon;

    /**
     * Returns the long version of dcat-admin.
     *
     * @return string The long application version
     */
    public static function getLongVersion()
    {
        return sprintf('Dcat Admin <comment>version</comment> <info>%s</info>', static::VERSION);
    }

    /**
     * Left sider-bar menu.
     *
     * @param Closure|null $builder
     * @return Menu
     */
    public static function menu(Closure $builder = null)
    {
        $menu = app('admin.menu');

        $builder && $builder($menu);

        return $menu;
    }

    /**
     * Set admin title.
     *
     * @return void
     */
    public static function setTitle($title)
    {
        static::$metaTitle = $title;
    }

    /**
     * Get admin title.
     *
     * @return string
     */
    public static function title()
    {
        return static::$metaTitle ?: config('admin.title');
    }


    /**
     * @param null|string $favicon
     *
     * @return string|void
     */
    public static function favicon($favicon = null)
    {
        if (is_null($favicon)) {
            return static::$favicon;
        }

        static::$favicon = $favicon;
    }

    /**
     * @param $repository
     * @param Closure|null $callback
     *
     * @return Grid
     */
    public static function grid($repository = null, \Closure $callback = null)
    {
        if ($repository instanceof \Closure) {
            return new Grid(null, $callback);
        }

        return new Grid($repository, $callback);
    }

    /**
     * @param $repository
     * @param Closure $callable
     *
     * @return Form
     */
    public static function form(Repository $repository, Closure $callable = null)
    {
        return new Form($repository, $callable);
    }

    /**
     * Build a tree.
     *
     * @param $model
     * @param Closure|null $callable
     *
     * @return Tree
     */
    public static function tree($model, Closure $callable = null)
    {
        return new Tree($model, $callable);
    }

    /**
     * Build show page.
     *
     * @example
     *     $show = Admin::show();
     *
     *     $show = Admin::show($id);
     *     $show = Admin::show(new Repository);
     *     $show = Admin::show(function (Show $show) {});
     *
     *     $show = Admin::show($id, new Repository);
     *     $show = Admin::show($id, function (Show $show) {});
     *     $show = Admin::show(new Repository, function (Show $show) {});
     *
     *     $show = Admin::show($id, new Repository, function (Show $show) {});
     *
     * @param $repository
     * @param mixed $callable
     *
     * @return Show
     */
    public static function show($id = null, $repository = null, \Closure $callable = null)
    {
        switch (func_num_args()) {
            case 0:
                $show = new Show();

                break;
            case 1:
                if ($id instanceof \Closure) {
                    $show = new Show(null, $id);
                } elseif ($id instanceof Repository) {
                    $show = new Show($id);
                } else {
                    $show = new Show();
                    $show->setKey($id);
                }

                break;
            case 2:
                if ($id instanceof Repository && $repository instanceof \Closure) {
                    $show = new Show($id, $repository);
                } elseif ($repository instanceof \Closure) {
                    $show = new Show(null, $repository);

                    $show->setKey($id);
                } elseif ($repository instanceof Repository) {
                    $show = new Show($repository);

                    $show->setKey($id);
                }
                break;
            case 3:
                $show = new Show($repository, $callable);

                $show->setKey($id);
        }

        return $show;
    }

    /**
     * @param Closure $callable
     *
     * @return Content
     */
    public static function content(Closure $callable = null)
    {
        return new Content($callable);
    }

    /**
     * Get current login user.
     *
     * @return Model|Authenticatable|HasPermissions
     */
    public static function user()
    {
        return static::guard()->user();
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|GuardHelpers
     */
    public static function guard()
    {
        return Auth::guard(config('admin.auth.guard') ?: 'admin');
    }

    /**
     * Navbar.
     *
     * @param Closure|null $builder
     *
     * @return Navbar
     */
    public static function navbar(Closure $builder = null)
    {
        $navbar = app('admin.navbar');

        $builder && $builder($navbar);

        return $navbar;
    }

    /**
     * Get section manager.
     *
     * @param Closure|null $builder
     *
     * @return SectionManager
     */
    public static function section(Closure $builder = null)
    {
        $manager = app('sectionManager');

        $builder && $builder($manager);

        return $manager;
    }

    /**
     * Register the auth routes.
     *
     * @return void
     */
    public static function registerAuthRoutes()
    {
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {

            /* @var \Illuminate\Routing\Router $router */
            $router->namespace('Dcat\Admin\Controllers')->group(function ($router) {

                /* @var \Illuminate\Routing\Router $router */
                $router->resource('auth/users', 'UserController');
                $router->resource('auth/menu', 'MenuController', ['except' => ['create', 'show']]);
                $router->resource('auth/logs', 'LogController', ['only' => ['index', 'destroy']]);

                if (config('admin.permission.enable')) {
                    $router->resource('auth/roles', 'RoleController');
                    $router->resource('auth/permissions', 'PermissionController');
                }
            });

            $authController = config('admin.auth.controller', AuthController::class);

            $router->get('auth/login', $authController.'@getLogin');
            $router->post('auth/login', $authController.'@postLogin');
            $router->get('auth/logout', $authController.'@getLogout');
            $router->get('auth/setting', $authController.'@getSetting');
            $router->put('auth/setting', $authController.'@putSetting');
        });
    }

    /**
     * Register the helpers routes.
     *
     * @return void
     */
    public static function registerHelperRoutes()
    {
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('helpers/scaffold', 'Dcat\Admin\Controllers\ScaffoldController@index');
            $router->post('helpers/scaffold', 'Dcat\Admin\Controllers\ScaffoldController@store');
            $router->post('helpers/scaffold/table', 'Dcat\Admin\Controllers\ScaffoldController@table');
            $router->get('helpers/routes', 'Dcat\Admin\Controllers\RouteController@index');
            $router->get('helpers/icons', 'Dcat\Admin\Controllers\IconController@index');
            $router->resource('helpers/extensions', 'Dcat\Admin\Controllers\ExtensionController', ['only' => ['index', 'update']]);
            $router->post('helpers/extensions/import', 'Dcat\Admin\Controllers\ExtensionController@import');
            $router->post('helpers/extensions/create', 'Dcat\Admin\Controllers\ExtensionController@create');
        });
    }

    /**
     * Create a repository instance.
     *
     * @param string $class
     * @param array  $args
     *
     * @return Repository
     */
    public static function repository($class, array $args = [])
    {
        $repository = $class;
        if (is_string($repository)) {
            $repository = new $class($args);
        }
        if (!$repository instanceof Repository) {
            throw new \InvalidArgumentException("[$class] must be a valid repository class.");
        }

        if ($repository instanceof Proxy) {
            return $repository;
        }

        return new Proxy($repository);
    }

    /**
     * Get all registered extensions.
     *
     * @return array
     */
    public static function getExtensions()
    {
        return static::$extensions;
    }

    /**
     * Get available extensions.
     *
     * @return Extension[]
     */
    public static function getAvailableExtensions()
    {
        if (static::$availableExtensions !== null) {
            return static::$availableExtensions;
        }

        static::$availableExtensions = [];
        foreach (static::$extensions as $k => $extension) {
            if (!config("admin-extensions.{$k}.enable")) {
                continue;
            }

            static::$availableExtensions[$k] = $extension::make();
        }

        return static::$availableExtensions;
    }

    /**
     * Extend a extension.
     *
     * @param string $class
     *
     * @return void
     */
    public static function extend(string $class)
    {
        static::$extensions[$class::NAME] = $class;
    }

    /**
     * Enable the extension.
     *
     * @param string $class
     * @param bool   $enable
     *
     * @return bool
     */
    public static function enableExtenstion(string $class, bool $enable = true)
    {
        if (!$class || !is_subclass_of($class, Extension::class)) {
            return false;
        }

        $name = $class::NAME;

        $config = (array) config('admin-extensions');

        $config[$name] = (array) ($config[$name] ?? []);

        $config[$name]['enable'] = $enable;

        return Helper::updateExtensionConfig($config);
    }

    /**
     * Disable the extension.
     *
     * @param string $class
     *
     * @return bool
     */
    public static function disableExtenstion(string $class)
    {
        return static::enableExtenstion($class, false);
    }

    /**
     * @return Handler
     */
    public static function makeExceptionHandler()
    {
        return app(
            config('admin.exception_handler') ?: Handler::class
        );
    }

    /**
     * @param callable $callback
     */
    public static function booting($callback)
    {
        Event::listen('admin.booting', $callback);
    }

    /**
     * @param callable $callback
     */
    public static function booted($callback)
    {
        Event::listen('admin.booted', $callback);
    }

    /**
     * Call booting callbacks.
     */
    public static function callBooting()
    {
        Event::dispatch('admin.booting');
    }

    /**
     * Call booted callbacks.
     */
    public static function callBooted()
    {
        Event::dispatch('admin.booted');
    }
}
