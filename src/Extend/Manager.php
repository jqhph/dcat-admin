<?php

namespace Dcat\Admin\Extend;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Models\Extension as ExtensionModel;
use Dcat\Admin\Support\Composer;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\Zip;
use Illuminate\Contracts\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
     * @var Filesystem
     */
    protected $files;

    public function __construct(Container $app)
    {
        $this->app = $app;

        $this->extensions = new Collection();

        $this->files = app('files');
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
        $this->extensions->each->boot();
    }

    /**
     * 判断扩展是否启用.
     *
     * @param  string|null  $name
     * @return bool
     */
    public function enabled(?string $name)
    {
        return (bool) optional($this->settings()->get($name))->is_enabled;
    }

    /**
     * 启用或禁用扩展.
     *
     * @param  string|null  $name
     * @param  bool  $enable
     * @return void
     */
    public function enable(?string $name, bool $enable = true)
    {
        $name = $this->getName($name);

        $extension = ExtensionModel::where('name', $name)->first();

        if (! $extension) {
            throw new RuntimeException(sprintf('Please install the extension(%s) first!', $name));
        }

        $extension->is_enabled = $enable;

        $extension->save();
    }

    /**
     * 加载扩展，注册自动加载规则.
     *
     * @return $this
     */
    public function load()
    {
        foreach ($this->getExtensionDirectories() as $directory) {
            try {
                $this->loadExtension($directory);
            } catch (\Throwable $e) {
                $this->reportException($e);
            }
        }

        return $this;
    }

    /**
     * 获取扩展路径.
     *
     * @param  string|ServiceProvider  $name
     * @param  string|null  $path
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
     * @param  string|ServiceProvider  $name
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
     * 判断插件是否存在.
     *
     * @param  string  $name
     * @return bool
     */
    public function has($name)
    {
        return $this->extensions->has($this->formatName($name));
    }

    /**
     * @param  string  $name
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
     * @param  string  $directory
     * @param  bool  $addPsr4
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
     * @param  string  $directory
     * @param  bool  $addPsr4
     * @return ServiceProvider
     */
    public function resolveExtension(string $directory, bool $addPsr4 = true)
    {
        $composerProperty = Composer::parse($directory.'/composer.json');

        $serviceProvider = $composerProperty->get('extra.dcat-admin');
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
     * @param  string  $dirPath
     * @return array
     */
    public function getExtensionDirectories($dirPath = null)
    {
        $extensions = [];

        $dirPath = $dirPath ?: admin_extension_path();

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
     * @param  \Dcat\Admin\Extend\ServiceProvider  $serviceProvider
     */
    public function addExtension(ServiceProvider $serviceProvider)
    {
        if (! $serviceProvider->getName()) {
            $json = dirname(dirname(Helper::guessClassFileName($serviceProvider))).'/composer.json';

            if (! is_file($json)) {
                throw new RuntimeException(sprintf('Error extension "%s"', get_class($serviceProvider)));
            }

            $serviceProvider->withComposerProperty(Composer::parse($json));
        }

        $this->extensions->put($serviceProvider->getName(), $serviceProvider);

        $this->app->instance($abstract = get_class($serviceProvider), $serviceProvider);
        $this->app->alias($abstract, $serviceProvider->getName());
    }

    /**
     * 获取扩展名称.
     *
     * @param $extension
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
     *
     * @param  string  $filePath
     * @param  bool  $force
     * @return string
     */
    public function extract($filePath, bool $force = false)
    {
        $filePath = is_file($filePath) ? $filePath : $this->getFilePath($filePath);

        $name = $this->extractZip($filePath, $force);

        @unlink($filePath);

        return $name;
    }

    /**
     * @param  string  $filePath
     * @param  bool  $force
     * @return bool
     */
    public function extractZip($filePath, bool $force = false)
    {
        // 创建临时目录.
        $tempPath = $this->makeTempDirectory();

        try {
            $filePath = is_file($filePath) ? $filePath : $this->getFilePath($filePath);

            if (! Zip::extract($filePath, $tempPath)) {
                throw new AdminException(sprintf('Unable to extract core file \'%s\'.', $filePath));
            }

            $extensions = $this->getExtensionDirectories($tempPath);

            // 无上层目录
            $directory = $tempPath;

            if (count($extensions) === 1) {
                // 双层目录
                $directory = current($extensions);
            } elseif (count($results = $this->scandir($tempPath)) === 1) {
                // 单层目录
                $directory = current($results);
            }

            // 验证扩展包内容是否正确.
            if (! $this->checkFiles($directory)) {
                throw new RuntimeException(sprintf('Error extension file "%s".', $filePath));
            }

            $composerProperty = Composer::parse($directory.'/composer.json');

            $extensionDir = admin_extension_path($composerProperty->name);

            if (! $force && is_dir($extensionDir)) {
                throw new RuntimeException(sprintf('The extension [%s] already exist!', $composerProperty->name));
            }

            if (! is_dir($extensionDir)) {
                $this->files->makeDirectory($extensionDir, 0755, true);
            }

            $this->files->copyDirectory($directory, $extensionDir);

            return $composerProperty->name;
        } finally {
            $this->files->deleteDirectory($tempPath);
        }
    }

    /**
     * 校验扩展包内容是否正确.
     *
     * @param $directory
     * @return bool
     */
    protected function checkFiles($directory)
    {
        if (
            ! is_dir($directory.'/src')
            || ! is_file($directory.'/composer.json')
            || ! is_file($directory.'/version.php')
        ) {
            return false;
        }

        $composerProperty = Composer::parse($directory.'/composer.json');

        if (! $composerProperty->name || ! $composerProperty->get('extra.dcat-admin')) {
            return false;
        }

        return true;
    }

    /**
     * 生成临时文件.
     *
     * @param  string  $fileCode  A unique file code
     * @return string Full path on the disk
     */
    protected function getFilePath($fileCode)
    {
        $name = md5($fileCode).'.arc';

        return $this->makeTempDirectory('extensions').'/'.$name;
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
                $this->settings = ExtensionModel::all()->keyBy('name');
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

    /**
     * 创建临时目录.
     *
     * @param  string  $dir
     * @return string
     */
    protected function makeTempDirectory($dir = null)
    {
        $tempDir = storage_path('tmp/'.($dir ?: time().Str::random()));

        if (! is_dir($tempDir)) {
            if (! $this->files->makeDirectory($tempDir, 0777, true)) {
                throw new RuntimeException(sprintf('Cannot write to directory "%s"', storage_path()));
            }
        }

        return $tempDir;
    }

    /**
     * 注册 PSR4 验证规则.
     *
     * @param  string  $directory
     * @param  array  $psr4
     */
    protected function registerPsr4($directory, array $psr4)
    {
        $classLoader = Admin::classLoader();

        foreach ($psr4 as $namespace => $path) {
            $path = $directory.'/'.trim($path, '/').'/';

            $classLoader->addPsr4($namespace, $path);
        }
    }

    /**
     * 上报异常.
     *
     * @param  \Throwable  $e
     */
    protected function reportException(\Throwable $e)
    {
        Admin::reportException($e);
    }

    /**
     * @param  string  $dir
     * @return array
     */
    protected function scandir($dir)
    {
        $results = [];

        foreach (scandir($dir) as $value) {
            if (
                $value !== '.'
                && $value !== '..'
                && is_dir($value = $dir.'/'.$value)
            ) {
                $results[] = $value;
            }
        }

        return $results;
    }
}
