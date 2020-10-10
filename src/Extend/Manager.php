<?php

namespace Dcat\Admin\Extend;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Models\Extension as ExtensionModel;
use Dcat\Admin\Support\Composer;
use Dcat\Admin\Support\Zip;
use Illuminate\Contracts\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Manager
{
    use Note;

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

    /**
     * @var ExtensionModel[]|Collection
     */
    protected $settings;

    /**
     * @var string
     */
    protected $tempDirectory;

    public function __construct(Container $app)
    {
        $this->app = $app;

        $this->extensions = new Collection();

        $this->tempDirectory = storage_path('extensions');

        if (! is_dir($this->tempDirectory)) {
            app('files')->makeDirectory($this->tempDirectory, 0777, true);
        }
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
            if ($this->enabled($extension->getName())) {
                $extension->boot();
            }
        }
    }

    /**
     * 判断扩展是否启用.
     *
     * @param string|null $slug
     *
     * @return bool
     */
    public function enabled(?string $slug)
    {
        return (bool) optional($this->settings()->get($slug))->is_enabled;
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
     * 获取扩展路径.
     *
     * @param string|ServiceProvider $name
     * @param string|null            $path
     *
     * @return string|void
     *
     * @throws \ReflectionException
     */
    public function path($name, $path = null)
    {
        if (! $extension = $this->get($name)) {
            return;
        }

        return $extension->path($path);
    }

    /**
     * 获取扩展对象.
     *
     * @param string|ServiceProvider $name
     *
     * @return ServiceProvider|null
     */
    public function get($name)
    {
        if ($name instanceof ServiceProvider) {
            return $name;
        }

        return $this->extensions->get($this->formatName($name));
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    protected function formatName($name)
    {
        if (! is_string($name)) {
            return $name;
        }

        return str_replace('/', '.', $name);
    }

    /**
     * 获取所有扩展.
     *
     * @return ServiceProvider[]|Collection
     */
    public function all()
    {
        return $this->extensions;
    }

    /**
     * 获取已启用的扩展.
     *
     * @return ServiceProvider[]|Collection
     */
    public function available()
    {
        return $this->all()->filter->enabled();
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

    /**
     * 添加扩展.
     *
     * @param \Dcat\Admin\Extend\ServiceProvider $serviceProvider
     */
    public function addExtension(ServiceProvider $serviceProvider)
    {
        $this->extensions->put($serviceProvider->getName(), $serviceProvider);
    }

    /**
     * 获取扩展名称.
     *
     * @param $extension
     *
     * @return string
     */
    public function getName($extension)
    {
        if ($extension instanceof ServiceProvider) {
            return $extension->getName();
        }

        return $this->formatName($extension);
    }

    /**
     * 解压缩扩展包.
     */
    public function extract($filePath)
    {
        $filePath = is_file($filePath) ? $filePath : $this->getFilePath($filePath);

        if (! Zip::extract($filePath, admin_extension_path())) {
            throw new AdminException(sprintf('Unable to extract core file \'%s\'.', $filePath));
        }

        @unlink($filePath);
    }

    /**
     * Calculates a file path for a file code
     *
     * @param string $fileCode A unique file code
     * @return string           Full path on the disk
     */
    protected function getFilePath($fileCode)
    {
        $name = md5($fileCode).'.arc';

        return $this->tempDirectory.'/'.$name;
    }

    /**
     * 获取配置.
     *
     * @return ExtensionModel[]|Collection
     */
    public function settings()
    {
        if ($this->settings === null) {
            try {
                $this->settings = ExtensionModel::all()->keyBy('slug');
            } catch (\Throwable $e) {
                $this->reportException($e);

                $this->settings = new Collection();
            }
        }

        return $this->settings;
    }

    /**
     * @return UpdateManager
     */
    public function updateManager()
    {
        return app('admin.extend.update');
    }

    /**
     * @return VersionManager
     */
    public function versionManager()
    {
        return app('admin.extend.version');
    }

    protected function registerPsr4($directory, array $psr4)
    {
        $classLoader = Admin::classLoader();

        foreach ($psr4 as $namespace => $path) {
            $path = $directory.'/'.trim($path, '/').'/';

            $classLoader->addPsr4($namespace, $path);
        }
    }

    protected function reportException(\Throwable $e)
    {
        logger()->error($e);
    }
}
