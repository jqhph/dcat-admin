<?php

namespace Dcat\Admin;

use Dcat\Admin\Layout\Asset;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Menu;
use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Layout\SectionManager;
use Dcat\Admin\Support\WebUploader;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\AdminCommand::class,
        Console\InstallCommand::class,
        Console\PublishCommand::class,
        Console\UninstallCommand::class,
        Console\ImportCommand::class,
        Console\CreateUserCommand::class,
        Console\ResetPasswordCommand::class,
        Console\ExtendCommand::class,
        Console\ExportSeedCommand::class,
        Console\IdeHelperCommand::class,
        Console\FormCommand::class,
        Console\ActionCommand::class,
        Console\MenuCacheCommand::class,
        Console\MinifyCommand::class,
        Console\AppCommand::class,
    ];

    /**
     * 开发环境命令.
     *
     * @var array
     */
    protected $devCommands = [
        Console\Development\LinkCommand::class,
    ];

    /**
     * @var array
     */
    protected $routeMiddleware = [
        'admin.auth'       => Middleware\Authenticate::class,
        'admin.pjax'       => Middleware\Pjax::class,
        'admin.log'        => Middleware\LogOperation::class,
        'admin.permission' => Middleware\Permission::class,
        'admin.bootstrap'  => Middleware\Bootstrap::class,
        'admin.session'    => Middleware\Session::class,
        'admin.upload'     => Middleware\WebUploader::class,
        'admin.app'        => Middleware\Application::class,
    ];

    /**
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.auth',
            'admin.pjax',
            'admin.log',
            'admin.bootstrap',
            'admin.permission',
            'admin.session',
            'admin.upload',
        ],
    ];

    public function boot()
    {
        $this->registerDefaultSections();
        $this->registerViews();
        $this->ensureHttps();
        $this->bootApplication();
        $this->registerPublishing();
        $this->compatibleBlade();
    }

    public function register()
    {
        require_once __DIR__.'/Support/AdminSection.php';

        $this->aliasAdmin();
        $this->registerExtensionProviders();
        $this->loadAdminAuthConfig();
        $this->registerRouteMiddleware();
        $this->registerServices();

        $this->commands($this->commands);

        if (config('app.debug')) {
            $this->commands($this->devCommands);
        }
    }

    protected function aliasAdmin()
    {
        if (! class_exists(\Admin::class)) {
            class_alias(Admin::class, \Admin::class);
        }
    }

    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin');
    }

    /**
     * 是否强制使用https.
     *
     * @return void
     */
    protected function ensureHttps()
    {
        if (config('admin.https') || config('admin.secure')) {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * 路由注册.
     */
    protected function bootApplication()
    {
        Admin::app()->boot();
    }

    /**
     * 禁止laravel 5.6或更高版本中启用双编码的默认特性.
     *
     * @return void
     */
    protected function compatibleBlade()
    {
        $bladeReflectionClass = new \ReflectionClass('\Illuminate\View\Compilers\BladeCompiler');
        if ($bladeReflectionClass->hasMethod('withoutDoubleEncoding')) {
            Blade::withoutDoubleEncoding();
        }
    }

    /**
     * 资源发布注册.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'dcat-admin-config');
            $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang')], 'dcat-admin-lang');
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'dcat-admin-migrations');
            $this->publishes([__DIR__.'/../resources/dist' => public_path(Admin::asset()->getRealPath('@admin'))], 'dcat-admin-assets');
        }
    }

    /**
     * 扩展注册.
     */
    public function registerExtensionProviders()
    {
        foreach (Admin::availableExtensions() as $extension) {
            if ($provider = $extension->serviceProvider()) {
                $this->app->register($provider);
            }
        }
    }

    /**
     * 设置 auth 配置.
     *
     * @return void
     */
    protected function loadAdminAuthConfig()
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));

        foreach ((array) config('admin.multi_app') as $app => $enable) {
            if ($enable) {
                config(Arr::dot(config($app.'.auth', []), 'auth.'));
            }
        }
    }

    /**
     * 默认 section 注册.
     */
    protected function registerDefaultSections()
    {
        Content::composing(function () {
            if (! admin_has_default_section(\AdminSection::NAVBAR_USER_PANEL)) {
                admin_inject_default_section(\AdminSection::NAVBAR_USER_PANEL, function () {
                    return view('admin::partials.navbar-user-panel', ['user' => Admin::user()]);
                });
            }

            if (! admin_has_default_section(\AdminSection::LEFT_SIDEBAR_USER_PANEL)) {
                admin_inject_default_section(\AdminSection::LEFT_SIDEBAR_USER_PANEL, function () {
                    return view('admin::partials.sidebar-user-panel', ['user' => Admin::user()]);
                });
            }

            // Register menu
            Admin::menu()->register();
        }, true);
    }

    protected function registerServices()
    {
        $this->app->singleton('admin.app', Application::class);
        $this->app->singleton('admin.asset', Asset::class);
        $this->app->singleton('admin.color', Color::class);
        $this->app->singleton('admin.sections', SectionManager::class);
        $this->app->singleton('admin.navbar', Navbar::class);
        $this->app->singleton('admin.menu', Menu::class);
        $this->app->singleton('admin.context', Fluent::class);
        $this->app->singleton('admin.web-uploader', WebUploader::class);
    }

    /**
     * 路由中间件注册.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        $router = $this->app->make('router');

        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            $router->aliasMiddleware($key, $middleware);
        }

        $disablePermission = ! config('admin.permission.enable');

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            if ($disablePermission && $middleware == 'admin.permission') {
                continue;
            }
            $router->middlewareGroup($key, $middleware);
        }
    }
}
