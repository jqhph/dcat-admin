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
    }

    public function getApps()
    {
        return $this->apps ?: ($this->apps = (array) config('admin.multi_app'));
    }

    public function getEnabledApps()
    {
        return array_filter($this->getApps());
    }

    public function switch(string $app = null)
    {
        $this->withName($app);

        $this->withConfig($this->name);
    }

    public function withName(string $app)
    {
        $this->name = $app;
    }

    public function getName()
    {
        return $this->name ?: static::DEFAULT;
    }

    public function boot()
    {
        $this->registerRoute(static::DEFAULT);

        if ($this->getApps()) {
            $this->registerMultiAppRoutes();

            $this->switch(static::DEFAULT);
        }
    }

    public function routes($pathOrCallback)
    {
        $this->switch(static::DEFAULT);
        $this->loadRoutesFrom($pathOrCallback, static::DEFAULT);

        if ($apps = $this->getApps()) {
            foreach ($apps as $app => $enable) {
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
        foreach ($this->getApps() as $app => $enable) {
            if ($enable) {
                $this->registerRoute($app);
            }
        }
    }

    public function getCurrentApiRoutePrefix()
    {
        return $this->getApiRoutePrefix($this->getName());
    }

    public function getApiRoutePrefix(?string $app = null)
    {
        return $this->getRoutePrefix($app).'dcat-api.';
    }

    public function getRoutePrefix(?string $app = null)
    {
        $app = $app ?: $this->getName();

        return 'dcat.'.$app.'.';
    }

    public function getRoute(?string $route, array $params = [], $absolute = true)
    {
        return route($this->getRoutePrefix().$route, $params, $absolute);
    }

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

    protected function withConfig(string $app)
    {
        if (! isset($this->configs[$app])) {
            $this->configs[$app] = config($app);
        }

        config(['admin' => $this->configs[$app]]);
    }

    protected function loadRoutesFrom($path, ?string $app)
    {
        Route::group(array_filter([
            'middleware' => 'admin.app:'.$app,
            'domain'     => config("{$app}.route.domain"),
            'as'         => $this->getRoutePrefix($app),
        ]), $path);
    }
}
