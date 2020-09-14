<?php

namespace Dcat\Admin\Extend;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Composer;
use Dcat\Test\Test;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Manager
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var ServiceProvider[]|Collection
     */
    protected $extensions;

    /**
     * @var array
     */
    protected $extensionPaths = [];

    public function __construct(Container $app)
    {
        $this->app = $app;

        $this->extensions = new Collection();
    }

    /**
     * 注册扩展.
     *
     * @return void
     */
    public function register()
    {
        $this->load();

        $this->extensions->each->register();
    }

    /**
     * 初始化扩展.
     */
    public function boot()
    {
        foreach ($this->extensions as $extension) {
            if ($extension->enabled()) {
                $extension->boot();
            }
        }
    }

    /**
     * 加载扩展，注册自动加载规则.
     *
     * @return void
     */
    protected function load()
    {
        foreach ($this->getExtensionDirectories() as $directory) {
            $this->loadExtension($directory);
        }
    }

    /**
     * 获取所有扩展.
     *
     * @return ServiceProvider[]|Collection
     */
    public function extensions()
    {
        return $this->extensions;
    }

    /**
     * 获取已启用的扩展.
     *
     * @return ServiceProvider[]|Collection
     */
    public function availableExtensions()
    {
        return $this->extensions()->filter(function (ServiceProvider $extension) {
            return $extension->enabled();
        });
    }

    /**
     * 加载扩展.
     *
     * @param string $directory
     * @param bool   $addPsr4
     *
     * @return ServiceProvider|null
     */
    public function loadExtension(string $directory, bool $addPsr4 = true)
    {
        if (array_key_exists($directory, $this->extensionPaths)) {
            return $this->extensionPaths[$directory];
        }

        $this->extensionPaths[$directory] = $serviceProvider = $this->resolveExtension($directory, $addPsr4);

        if ($serviceProvider) {
            $this->addExtension($serviceProvider);
        }

        return $serviceProvider;
    }

    /**
     * 获取扩展类实例.
     *
     * @param string $directory
     * @param bool   $addPsr4
     *
     * @return ServiceProvider
     */
    public function resolveExtension(string $directory, bool $addPsr4 = true)
    {
        $composerProperty = Composer::parse($directory.'/composer.json');

        $serviceProvider = $composerProperty->get('dcat-admin.provider');
        $psr4 = $composerProperty->get('autoload.psr-4');

        if (! $serviceProvider || ! $psr4) {
            return;
        }

        if ($addPsr4) {
            $this->registerPsr4($directory, $psr4);
        }

        $serviceProvider = new $serviceProvider($this->app);

        return $serviceProvider->withComposerProperty($composerProperty);
    }

    /**
     * 获取扩展目录.
     *
     * @return array
     */
    public function getExtensionDirectories()
    {
        $extensions = [];

        $dirPath = admin_extension_path();

        if (! is_dir($dirPath)) {
            return $extensions;
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::FOLLOW_SYMLINKS)
        );
        $it->setMaxDepth(2);
        $it->rewind();

        while ($it->valid()) {
            if ($it->getDepth() > 1 && $it->getFilename() === 'composer.json') {
                $extensions[] = dirname($it->getPathname());
            }

            $it->next();
        }

        return $extensions;
    }

    public function addExtension(ServiceProvider $serviceProvider)
    {
        $this->extensions->put($serviceProvider->name(), $serviceProvider);
    }

    protected function registerPsr4($directory, array $psr4)
    {
        $classLoader = Admin::classLoader();

        foreach ($psr4 as $namespace => $path) {
            $path = $directory.'/'.trim($path, '/').'/';

            $classLoader->addPsr4($namespace, $path);
        }
    }
}
