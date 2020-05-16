<?php

namespace Dcat\Admin;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Route;

class Application
{
    const DEFAULT = 'admin';

    /**
     * @var Container
     */
    protected $app;

    /**
     * 所有启用应用的配置.
     *
     * @var array
     */
    protected $configs = [];

    /**
     * 当前应用名称.
     *
     * @var string
     */
    protected $name;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * 设置当前应用配置.
     *
     * @param string $app
     */
    public function current(string $app = null)
    {
        $this->withName($app);

        $this->withConfig($this->name);
    }

    /**
     * 设置应用名称.
     *
     * @param string $app
     */
    public function withName(string $app)
    {
        $this->name = $app;
    }

    /**
     * 获取当前应用名称
     *
     * @return string
     */
    public function getName()
    {
        return $this->name ?: static::DEFAULT;
    }

    /**
     * 注册应用.
     */
    public function boot()
    {
        $this->registerRoute(static::DEFAULT);

        if ($this->app->runningInConsole()) {
            return;
        }
        foreach ((array) config('admin.multi_app') as $app => $enable) {
            if ($enable) {
                $this->registerRoute($app);
            }
        }
    }

    /**
     * @return string
     */
    public function getCurrentApiRoutePrefix()
    {
        return $this->getApiRoutePrefix($this->getName());
    }

    /**
     * @param string|null $app
     *
     * @return string
     */
    public function getApiRoutePrefix(?string $app)
    {
        return "dcat.api.{$app}.";
    }

    /**
     * 注册应用路由.
     *
     * @param string|null $app
     */
    protected function registerRoute(?string $app)
    {
        $this->withConfig($app);

        Admin::registerApiRoutes($this->getApiRoutePrefix($app));

        if (is_file($routes = admin_path('routes.php'))) {
            $this->loadRoutesFrom($routes, $app);
        }
    }

    /**
     * 设置应用配置.
     *
     * @param string $app
     */
    protected function withConfig(string $app)
    {
        if (! isset($this->configs[$app])) {
            $this->configs[$app] = config($app);
            $this->configs[$app]['current_app'] = $app;
        }

        config(['admin' => $this->configs[$app]]);
    }

    /**
     * 加载路由文件.
     *
     * @param  string  $path
     * @param  string  $app
     *
     * @return void
     */
    protected function loadRoutesFrom(string $path, ?string $app)
    {
        if (! $this->app->routesAreCached()) {
            Route::middleware('admin.app:'.$app)->group($path);
        }
    }
}
