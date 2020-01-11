<?php

namespace Dcat\Admin;

use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Menu;
use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Layout\SectionManager;
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
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin.auth'       => Middleware\Authenticate::class,
        'admin.pjax'       => Middleware\Pjax::class,
        'admin.log'        => Middleware\LogOperation::class,
        'admin.permission' => Middleware\Permission::class,
        'admin.bootstrap'  => Middleware\Bootstrap::class,
        'admin.session'    => Middleware\Session::class,
    ];

    /**
     * The application's route middleware groups.
     *
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
        ],
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerDefaultSections();
        $this->registerViews();
        $this->ensureHttps();
        $this->registerRoutes();
        $this->registerPublishing();
        $this->compatibleBlade();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        require_once __DIR__.'/Support/AdminSection.php';

        $this->registerExtensionProviders();
        $this->loadAdminAuthConfig();
        $this->registerRouteMiddleware();
        $this->registerServices();

        $this->commands($this->commands);
    }

    /**
     * Register the view file namespace.
     */
    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin');
    }

    /**
     * Force to set https scheme if https enabled.
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
     * Register routes.
     */
    protected function registerRoutes()
    {
        if (is_file($routes = admin_path('routes.php'))) {
            $this->loadRoutesFrom($routes);
        }
    }

    /**
     * Remove default feature of double encoding enable in laravel 5.6 or later.
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
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'dcat-admin-config');
            $this->publishes([__DIR__.'/../resources/lang' => resource_path('lang')], 'dcat-admin-lang');
            $this->publishes([__DIR__.'/../database/migrations' => database_path('migrations')], 'dcat-admin-migrations');
            $this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/dcat-admin')], 'dcat-admin-assets');
        }
    }

    /**
     * Register the service provider of extensions.
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
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadAdminAuthConfig()
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));
    }

    /**
     * Register default sections.
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

    /**
     * Register admin services.
     */
    protected function registerServices()
    {
        $this->app->singleton('admin.sections', SectionManager::class);
        $this->app->singleton('admin.navbar', Navbar::class);
        $this->app->singleton('admin.menu', Menu::class);
        $this->app->singleton('admin.context', Fluent::class);
    }

    /**
     * Register the route middleware.
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
