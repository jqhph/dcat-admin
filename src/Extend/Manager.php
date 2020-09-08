<?php

namespace Dcat\Admin\Extend;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Composer;
use Illuminate\Support\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Manager
{
    /**
     * @var ServiceProvider[]|Collection
     */
    protected $extensions;

    /**
     * @var array
     */
    protected $extensionPaths = [];

    public function __construct()
    {
        $this->extensions = new Collection();
    }

    /**
     * 加载扩展，注册自动加载规则.
     *
     * @return void
     */
    public function load()
    {
        foreach ($this->getExtensionDirectories() as $directory) {
            $this->loadExtension($directory);
        }
    }

    /**
     * 加载扩展.
     *
     * @param string $directory
     */
    public function loadExtension(string $directory)
    {
        if (array_key_exists($directory, $this->extensionPaths)) {
            return $this->extensionPaths[$directory];
        }
        $this->extensionPaths[$directory] = null;

        $composerProperty = Composer::parse($directory.'/composer.json');

        $serviceProvider = $composerProperty->get('dcat-admin.provider');
        $psr4 = $composerProperty->get('autoload.psr-4');

        if (! $serviceProvider || ! $psr4) {
            return;
        }

        if (! class_exists($serviceProvider)) {
            $classLoader = Admin::classLoader();

            foreach ($psr4 as $namespace => $path) {
                //dd($namespace, $directory.'/'.trim($path, '/').'/');
                $classLoader->addPsr4($namespace, $directory.'/'.trim($path, '/').'/');
            }
        }

        /* @var ServiceProvider $serviceProvider */
        $serviceProvider = new $serviceProvider();

        $this->extensions->put($serviceProvider->name(), $serviceProvider);

        $this->extensionPaths[$directory] = $serviceProvider;

        return $serviceProvider;
    }

    /**
     * 注册扩展.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->extensions as $extension) {
            $extension->register();
        }
    }

    public function boot()
    {
        foreach ($this->extensions as $extension) {
            $extension->boot();
        }
    }

    /**
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
            if ($it->getDepth() > 1 && $it->isFile() && $it->getFilename() === 'composer.json') {
                $extensions[] = dirname($it->getPathname());
            }

            $it->next();
        }

        return $extensions;
    }
}
