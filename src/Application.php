<?php

namespace Dcat\Admin;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Traits\Macroable;

class Application
{
    use Macroable;

    const DEFAULT = 'admin';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $apps;

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
        $this->container = $app;
        $this->apps = (array) config('admin.multi_app');
    }

    /**
     * 设置当前应用配置.
     *
     * @param string $app
     */
    public function switch(string $app = null)
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
     * 获取当前应用名称.
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

        if ($this->apps) {
            $this->registerMultiAppRoutes();

            $this->switch(static::DEFAULT);
        }
    }

    /**
     * 注册路由.
     *
     * @param string|\Closure $pathOrCallback
     */
    public function routes($pathOrCallback)
    {
        $this->loadRoutesFrom($pathOrCallback, static::DEFAULT);

        if ($this->apps) {
            foreach ($this->apps as $app => $enable) {
                if ($enable) {
                    $this->switch($app);

                    $this->loadRoutesFrom($pathOrCallback, $app);
                }
            }

            $this->switch(static::DEFAULT);
        }
    }

    protected function registerMultiAppRoutes()
    {
        foreach ($this->apps as $app => $enable) {
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
    public function getApiRoutePrefix(?string $app = null)
    {
        return $this->getRoutePrefix($app).'dcat-api.';
    }

    /**
     * 获取路由别名前缀.
     *
     * @param string|null $app
     *
     * @return string
     */
    public function getRoutePrefix(?string $app = null)
    {
        $app = $app ?: $this->getName();

        return 'dcat.'.$app.'.';
    }

    /**
     * 获取路由别名.
     *
     * @param string|null $route
     * @param array $params
     * @param bool $absolute
     *
     * @return string
     */
    public function getRoute(?string $route, array $params = [], $absolute = true)
    {
        return route($this->getRoutePrefix().$route, $params, $absolute);
    }

    /**
     * 注册应用路由.
     *
     * @param string|null $app
     */
    protected function registerRoute(?string $app)
    {
        $this->switch($app);

        $this->loadRoutesFrom(function () {
            Admin::registerApiRoutes();
        }, $app);

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
    protected function loadRoutesFrom($path, ?string $app)
    {
        Route::group(array_filter([
            'middleware' => 'admin.app:'.$app,
            'domain'     => config("{$app}.route.domain"),
            'as'         => $this->getRoutePrefix($app),
        ]), $path);
    }
}
