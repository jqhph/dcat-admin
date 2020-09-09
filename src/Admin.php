<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Http\Controllers\AuthController;
use Dcat\Admin\Http\Exception\Handler;
use Dcat\Admin\Layout\Menu;
use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Layout\SectionManager;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Support\Composer;
use Dcat\Admin\Traits\HasAssets;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

/**
 * Class Admin.
 */
class Admin
{
    use HasAssets;

    /**
     * 版本号.
     *
     * @var string
     */
    const VERSION = '1.7.3';

    /**
     * @var string
     */
    public static $metaTitle;

    /**
     * @var string
     */
    public static $favicon;

    /**
     * @var array
     */
    public static $jsVariables = [];

    /**
     * @var string
     */
    public static $pjaxContainerId = 'pjax-container';

    /**
     * 版本.
     *
     * @return string
     */
    public static function longVersion()
    {
        return sprintf('Dcat Admin <comment>version</comment> <info>%s</info>', static::VERSION);
    }

    /**
     * @return Color
     */
    public static function color()
    {
        return app('admin.color');
    }

    /**
     * 菜单管理.
     *
     * @param Closure|null $builder
     *
     * @return Menu
     */
    public static function menu(Closure $builder = null)
    {
        $menu = app('admin.menu');

        $builder && $builder($menu);

        return $menu;
    }

    /**
     * 设置 title.
     *
     * @return string|void
     */
    public static function title($title = null)
    {
        if ($title === null) {
            return static::$metaTitle ?: config('admin.title');
        }

        static::$metaTitle = $title;
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
     * 获取登录用户模型.
     *
     * @return Model|Authenticatable|HasPermissions
     */
    public static function user()
    {
        return static::guard()->user();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|GuardHelpers
     */
    public static function guard()
    {
        return Auth::guard(config('admin.auth.guard') ?: 'admin');
    }

    /**
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
     * 禁用pjax.
     */
    public static function disablePjax()
    {
        static::$pjaxContainerId = null;
    }

    /**
     * section.
     *
     * @param Closure|null $builder
     *
     * @return SectionManager
     */
    public static function section(Closure $builder = null)
    {
        $manager = app('admin.sections');

        $builder && $builder($manager);

        return $manager;
    }

    /**
     * 配置.
     *
     * @return \Dcat\Admin\Support\Setting
     */
    public static function setting()
    {
        return app('admin.setting');
    }

    /**
     * 注册路由.
     *
     * @return void
     */
    public static function routes()
    {
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
            'as'         => static::app()->getName().'.',
        ];

        if (config('admin.auth.enable', true)) {
            app('router')->group($attributes, function ($router) {
                /* @var \Illuminate\Routing\Router $router */
                $router->namespace('Dcat\Admin\Http\Controllers')->group(function ($router) {
                    /* @var \Illuminate\Routing\Router $router */
                    $router->resource('auth/users', 'UserController');
                    $router->resource('auth/menu', 'MenuController', ['except' => ['create', 'show']]);

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

        static::registerHelperRoutes();
    }

    /**
     * 注册api路由.
     *
     * @param string $as
     *
     * @return void
     */
    public static function registerApiRoutes(string $as = null)
    {
        $attributes = [
            'prefix'     => admin_base_path('dcat-api'),
            'middleware' => config('admin.route.middleware'),
            'as'         => $as ?: static::app()->getApiRoutePrefix(Application::DEFAULT),
        ];

        app('router')->group($attributes, function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->namespace('Dcat\Admin\Http\Controllers')->group(function ($router) {
                /* @var \Illuminate\Routing\Router $router */
                $router->post('action', 'HandleActionController@handle')->name('action');
                $router->post('form', 'HandleFormController@handle')->name('form');
                $router->post('form/upload', 'HandleFormController@uploadFile')->name('form.upload');
                $router->post('form/destroy-file', 'HandleFormController@destroyFile')->name('form.destroy-file');
                $router->post('value', 'ValueController@handle')->name('value');
                $router->get('render', 'RenderableController@handle')->name('render');
                $router->post('tinymce/upload', 'TinymceController@upload')->name('tinymce.upload');
                $router->post('editor-md/upload', 'EditorMDController@upload')->name('editor-md.upload');
            });
        });
    }

    /**
     * 注册开发工具路由.
     *
     * @return void
     */
    public static function registerHelperRoutes()
    {
        if (! config('admin.helpers.enable', true) || ! config('app.debug')) {
            return;
        }

        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
            'as'         => static::app()->getName().'.',
        ];

        app('router')->group($attributes, function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('helpers/scaffold', 'Dcat\Admin\Http\Controllers\ScaffoldController@index');
            $router->post('helpers/scaffold', 'Dcat\Admin\Http\Controllers\ScaffoldController@store');
            $router->post('helpers/scaffold/table', 'Dcat\Admin\Http\Controllers\ScaffoldController@table');
            $router->get('helpers/icons', 'Dcat\Admin\Http\Controllers\IconController@index');
            $router->resource('helpers/extensions', 'Dcat\Admin\Http\Controllers\ExtensionController', ['only' => ['index', 'store', 'update']]);
            $router->post('helpers/extensions/import', 'Dcat\Admin\Http\Controllers\ExtensionController@import');
        });
    }

    /**
     * 创建数据仓库实例.
     *
     * @param string|Repository|Model|Builder $value
     * @param array                   $args
     *
     * @return Repository
     */
    public static function repository($repository, array $args = [])
    {
        if (is_string($repository)) {
            $repository = new $repository($args);
        }

        if ($repository instanceof Model || $repository instanceof Builder) {
            $repository = EloquentRepository::make($repository);
        }

        if (! $repository instanceof Repository) {
            $class = is_object($repository) ? get_class($repository) : $repository;

            throw new \InvalidArgumentException("The class [{$class}] must be a type of [".Repository::class.'].');
        }

        return $repository;
    }

    /**
     * 应用管理.
     *
     * @return Application
     */
    public static function app()
    {
        return app('admin.app');
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
     * @return void
     */
    public static function callBooting()
    {
        Event::dispatch('admin.booting');
    }

    /**
     * @return void
     */
    public static function callBooted()
    {
        Event::dispatch('admin.booted');
    }

    /**
     * @return Fluent
     */
    public static function context()
    {
        return app('admin.context');
    }

    /**
     * @param array|string $name
     *
     * @return void
     */
    public static function addIgnoreQueryName($name)
    {
        $context = static::context();

        $ignoreQueries = $context->ignoreQueries ?? [];

        $context->ignoreQueries = array_merge($ignoreQueries, (array) $name);
    }

    /**
     * @return array
     */
    public static function getIgnoreQueryNames()
    {
        return static::context()->ignoreQueries ?? [];
    }

    /**
     * 插件管理.
     *
     * @return \Dcat\Admin\Extend\Manager
     */
    public static function extensions()
    {
        return app('admin.extend');
    }

    /**
     * 类自动加载器.
     *
     * @return \Composer\Autoload\ClassLoader
     */
    public static function classLoader()
    {
        return Composer::loader();
    }

    /**
     * 获取js配置.
     *
     * @return string
     */
    public static function jsVariables()
    {
        $sidebarStyle = config('admin.layout.sidebar_style') ?: 'light';

        static::$jsVariables['pjax_container_selector'] = '#'.static::$pjaxContainerId;
        static::$jsVariables['token'] = csrf_token();
        static::$jsVariables['lang'] = __('admin.client') ?: [];
        static::$jsVariables['colors'] = static::color()->all();
        static::$jsVariables['dark_mode'] = Str::contains(config('admin.layout.body_class'), 'dark-mode');
        static::$jsVariables['sidebar_dark'] = config('admin.layout.sidebar_dark') || ($sidebarStyle === 'dark');
        static::$jsVariables['sidebar_light_style'] = in_array($sidebarStyle, ['dark', 'light'], true) ? 'sidebar-light-primary' : 'sidebar-primary';

        return json_encode(static::$jsVariables);
    }
}
