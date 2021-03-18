<?php

namespace Dcat\Admin\Extend;

use Dcat\Admin\Admin;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Support\ComposerProperty;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Symfony\Component\Console\Output\NullOutput;

abstract class ServiceProvider extends LaravelServiceProvider
{
    use CanImportMenu;

    const TYPE_THEME = 'theme';

    /**
     * @var ComposerProperty
     */
    public $composerProperty;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $js = [];

    /**
     * @var array
     */
    protected $css = [];

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    public $output;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * @var array
     */
    protected $exceptRoutes = [];

    public function __construct($app)
    {
        parent::__construct($app);

        $this->output = new NullOutput();
    }

    /**
     * {@inheritdoc}
     */
    final public function boot()
    {
        $this->autoRegister();

        if ($this->disabled()) {
            return;
        }

        $this->init();
    }

    /**
     * 初始化操作.
     *
     * @return void
     */
    public function init()
    {
        if ($views = $this->getViewPath()) {
            $this->loadViewsFrom($views, $this->getName());
        }

        if ($lang = $this->getLangPath()) {
            $this->loadTranslationsFrom($lang, $this->getName());
        }

        if ($routes = $this->getRoutes()) {
            $this->registerRoutes($routes);
        }

        if ($this->middleware()) {
            $this->addMiddleware();
        }

        if ($this->exceptRoutes) {
            $this->addExceptRoutes();
        }

        $this->aliasAssets();
    }

    /**
     * 自动注册扩展.
     */
    protected function autoRegister()
    {
        if ($this->getName()) {
            return;
        }

        Admin::extension()->addExtension($this);
    }

    /**
     * 获取扩展名称.
     *
     * @return string|void
     */
    final public function getName()
    {
        return $this->name ?: ($this->name = str_replace('/', '.', $this->getPackageName()));
    }

    /**
     * 获取扩展别名.
     *
     * @return string
     */
    public function getAlias()
    {
        if (! $this->composerProperty) {
            return;
        }

        return $this->composerProperty->alias;
    }

    /**
     * 获取包名.
     *
     * @return string|void
     */
    final public function getPackageName()
    {
        if (! $this->packageName) {
            if (! $this->composerProperty) {
                return;
            }

            $this->packageName = $this->composerProperty->name;
        }

        return $this->packageName;
    }

    /**
     * 获取插件类型.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 获取当前已安装版本.
     *
     * @return string
     */
    final public function getVersion()
    {
        return Admin::extension()->versionManager()->getCurrentVersion($this);
    }

    /**
     * 获取当前最新版本.
     *
     * @return string
     */
    final public function getLatestVersion()
    {
        return Admin::extension()->versionManager()->getFileVersions($this);
    }

    /**
     * 获取当前本地最新版本.
     *
     * @return string
     */
    final public function getLocalLatestVersion()
    {
        return last(
            array_keys(Admin::extension()->versionManager()->getFileVersions($this))
        );
    }

    /**
     * 获取扩展包路径.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    public function path(?string $path = null)
    {
        if (! $this->path) {
            $this->path = realpath(dirname((new \ReflectionClass(static::class))->getFileName()).'/..');

            if (! is_dir($this->path)) {
                throw new RuntimeException("The {$this->path} is not a directory.");
            }
        }

        $path = ltrim($path, '/');

        return $path ? $this->path.'/'.$path : $this->path;
    }

    /**
     * 获取logo路径.
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    public function getLogoPath()
    {
        return $this->path('logo.png');
    }

    /**
     * @return string
     */
    public function getLogoBase64()
    {
        try {
            $logo = $this->getLogoPath();

            if (is_file($logo) && $file = fopen($logo, 'rb', 0)) {
                $content = fread($file, filesize($logo));
                fclose($file);
                $base64 = chunk_split(base64_encode($content));

                return 'data:image/png;base64,'.$base64;
            }
        } catch (\ReflectionException $e) {
        }
    }

    /**
     * 判断扩展是否启用.
     *
     * @return bool
     */
    final public function enabled()
    {
        return Admin::extension()->enabled($this->getName());
    }

    /**
     * 判断扩展是否禁用.
     *
     * @return bool
     */
    final public function disabled()
    {
        return ! $this->enabled();
    }

    /**
     * 获取或保存配置.
     *
     * @param string $key
     * @param null   $default
     *
     * @return mixed
     */
    final public function config($key = null, $default = null)
    {
        if ($this->config === null) {
            $this->initConfig();
        }

        if (is_array($key)) {
            $this->saveConfig($key);

            return;
        }

        if ($key === null) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    /**
     * 保存配置.
     *
     * @param array $config
     */
    public function saveConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        Admin::setting()->save([$this->getConfigKey() => $this->serializeConfig($this->config)]);
    }

    /**
     * 初始化配置.
     */
    protected function initConfig()
    {
        $this->config = Admin::setting()->get($this->getConfigKey());
        $this->config = $this->config ? $this->unserializeConfig($this->config) : [];
    }

    /**
     * 更新扩展.
     *
     * @param string $currentVersion
     * @param string $stopOnVersion
     *
     * @throws \Exception
     */
    public function update($currentVersion, $stopOnVersion)
    {
        $this->refreshMenu();
    }

    /**
     * 卸载扩展.
     */
    public function uninstall()
    {
        $this->flushMenu();
    }

    /**
     * 发布静态资源.
     */
    public function publishable()
    {
        if ($assets = $this->getAssetPath()) {
            $this->publishes([
                $assets => $this->getPublishsPath(),
            ], $this->getName());
        }
    }

    /**
     * 获取资源发布路径.
     *
     * @return string
     */
    protected function getPublishsPath()
    {
        return public_path(
            Admin::asset()->getRealPath('@extension/'.str_replace('.', '/', $this->getName()))
        );
    }

    /**
     * 注册路由.
     *
     * @param $callback
     */
    public function registerRoutes($callback)
    {
        Admin::app()->routes(function ($router) use ($callback) {
            $router->group([
                'prefix'     => config('admin.route.prefix'),
                'middleware' => config('admin.route.middleware'),
            ], $callback);
        });
    }

    /**
     * 获取中间件.
     *
     * @return array
     */
    protected function middleware()
    {
        return $this->middleware;
    }

    /**
     * 注册中间件.
     */
    protected function addMiddleware()
    {
        $adminMiddleware = (array) config('admin.route.middleware');
        $middleware = $this->middleware();

        $before = $middleware['before'] ?? [];
        $middle = $middleware['middle'] ?? [];
        $after = $middleware['after'] ?? [];

        $this->mixMiddleware($middle);

        config([
            'admin.route.middleware' => array_merge((array) $before, $adminMiddleware, (array) $after),
        ]);
    }

    protected function mixMiddleware(array $middle)
    {
        Admin::mixMiddlewareGroup($middle);
    }

    /**
     * 配置需要跳过权限认证和登录认证的路由.
     */
    protected function addExceptRoutes()
    {
        if (! empty($this->exceptRoutes['permission'])) {
            Admin::context()->merge('permission.except', (array) $this->exceptRoutes['permission']);
        }

        if (! empty($this->exceptRoutes['auth'])) {
            Admin::context()->merge('auth.except', (array) $this->exceptRoutes['auth']);
        }
    }

    /**
     * 获取静态资源路径.
     *
     * @return string
     */
    final public function getAssetPath()
    {
        return $this->path('resources/assets');
    }

    /**
     * 获取视图路径.
     *
     * @return string
     */
    final public function getViewPath()
    {
        return $this->path('resources/views');
    }

    /**
     * 获取语言包路径.
     *
     * @return string
     */
    final public function getLangPath()
    {
        return $this->path('resources/lang');
    }

    /**
     * 获取路由地址.
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    final public function getRoutes()
    {
        $path = $this->path('src/Http/routes.php');

        return is_file($path) ? $path : null;
    }

    /**
     * @param ComposerProperty $composerProperty
     *
     * @return $this
     */
    public function withComposerProperty(ComposerProperty $composerProperty)
    {
        $this->composerProperty = $composerProperty;

        return $this;
    }

    /**
     * 获取或保存配置.
     *
     * @param string $key
     * @param string $value
     *
     * @return mixed
     */
    public static function setting($key = null, $value = null)
    {
        $extension = static::instance();

        if ($extension && $extension instanceof ServiceProvider) {
            return $extension->config($key, $value);
        }
    }

    /**
     * 翻译.
     *
     * @param string $key
     * @param array  $replace
     * @param null   $locale
     *
     * @return array|string|null
     */
    public static function trans($key, $replace = [], $locale = null)
    {
        return trans(static::instance()->getName().'::'.$key, $replace, $locale);
    }

    /**
     * 获取自身实例.
     *
     * @return $this
     */
    public static function instance()
    {
        return app(static::class);
    }

    /**
     * 注册别名.
     */
    protected function aliasAssets()
    {
        $asset = Admin::asset();

        // 注册静态资源路径别名
        $asset->alias($this->getName().'.path', '@extension/'.$this->getPackageName());

        if ($this->js || $this->css) {
            $asset->alias($this->getName(), [
                'js'  => $this->formatAssetFiles($this->js),
                'css' => $this->formatAssetFiles($this->css),
            ]);
        }
    }

    /**
     * @param string|array $files
     *
     * @return mixed
     */
    protected function formatAssetFiles($files)
    {
        if (is_array($files)) {
            return array_map([$this, 'formatAssetFiles'], $files);
        }

        return '@'.$this->getName().'.path/'.trim($files, '/');
    }

    /**
     * 配置key.
     *
     * @return mixed
     */
    protected function getConfigKey()
    {
        return str_replace('.', ':', $this->getName());
    }

    /**
     * @param $config
     *
     * @return false|string
     */
    protected function serializeConfig($config)
    {
        return json_encode($config);
    }

    /**
     * @param $config
     *
     * @return array
     */
    protected function unserializeConfig($config)
    {
        return json_decode($config, true);
    }
}
