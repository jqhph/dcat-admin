<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Support\Helper;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ExtensionMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:ext-make 
    {name : The name of the extension. Eg: author-name/extension-name} 
    {--namespace= : The namespace of the extension.}
    {--theme}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a dcat-admin extension';

    /**
     * @var string
     */
    protected $basePath = '';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $extensionName;

    /**
     * @var string
     */
    protected $package;

    /**
     * @var string
     */
    protected $extensionDir;

    /**
     * @var array
     */
    protected $dirs = [
        'updates',
        'resources/assets/css',
        'resources/assets/js',
        'resources/views',
        'resources/lang',
        'src/Models',
        'src/Http/Controllers',
        'src/Http/Middleware',
    ];

    protected $themeDirs = [
        'updates',
        'resources/assets/css',
        'resources/views',
        'src',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        $this->extensionDir = admin_extension_path();

        if (! file_exists($this->extensionDir)) {
            $this->makeDir();
        }

        $this->package = str_replace('.', '/', $this->argument('name'));
        $this->extensionName = str_replace('/', '.', $this->package);

        $this->basePath = rtrim($this->extensionDir, '/').'/'.ltrim($this->package, '/');

        if (is_dir($this->basePath)) {
            return $this->error(sprintf('The extension [%s] already exists!', $this->package));
        }

        InputExtensionName :
        if (! Helper::validateExtensionName($this->package)) {
            $this->package = $this->ask("[$this->package] is not a valid package name, please input a name like (<vendor>/<name>)");
            goto InputExtensionName;
        }

        $this->makeDirs();
        $this->makeFiles();

        $this->info("The extension scaffolding generated successfully. \r\n");
        $this->showTree();
    }

    /**
     * Show extension scaffolding with tree structure.
     */
    protected function showTree()
    {
        if ($this->option('theme')) {
            $tree = <<<TREE
{$this->extensionPath()}
    ├── README.md
    ├── composer.json
    ├── version.php
    ├── updates
    ├── resources
    │   ├── lang
    │   ├── assets
    │   │   └── css
    │   │       └── index.css
    │   └── views
    └── src
        ├── {$this->className}ServiceProvider.php
        └── Setting.php
TREE;
        } else {
            $tree = <<<TREE
{$this->extensionPath()}
    ├── README.md
    ├── composer.json
    ├── version.php
    ├── updates
    ├── resources
    │   ├── lang
    │   ├── assets
    │   │   ├── css
    │   │   │   └── index.css
    │   │   └── js
    │   │       └── index.js
    │   └── views
    │       └── index.blade.php
    └── src
        ├── {$this->className}ServiceProvider.php
        ├── Setting.php
        ├── Models
        └── Http
            ├── routes.php
            ├── Middleware
            └── Controllers
                └── {$this->className}Controller.php
TREE;
        }

        $this->info($tree);
    }

    /**
     * Make extension files.
     */
    protected function makeFiles()
    {
        $this->namespace = $this->getRootNameSpace();

        $this->className = $this->getClassName();

        // copy files
        $this->copyFiles();

        // make composer.json
        $composerContents = str_replace(
            ['{package}', '{alias}', '{namespace}', '{className}'],
            [$this->package, '', str_replace('\\', '\\\\', $this->namespace).'\\\\', $this->className],
            file_get_contents(__DIR__.'/stubs/extension/composer.json.stub')
        );
        $this->putFile('composer.json', $composerContents);

        // make composer.json
        $settingContents = str_replace(
            ['{namespace}'],
            [$this->namespace],
            file_get_contents(__DIR__.'/stubs/extension/setting.stub')
        );
        $this->putFile('src/Setting.php', $settingContents);

        $basePackage = Helper::slug(basename($this->package));

        // make class
        $classContents = str_replace(
            ['{namespace}', '{className}', '{title}', '{path}', '{basePackage}', '{property}', '{registerTheme}'],
            [
                $this->namespace,
                $this->className,
                Str::title($this->className),
                $basePackage,
                $basePackage,
                $this->makeProviderContent(),
                $this->makeRegisterThemeContent(),
            ],
            file_get_contents(__DIR__.'/stubs/extension/extension.stub')
        );
        $this->putFile("src/{$this->className}ServiceProvider.php", $classContents);

        if (! $this->option('theme')) {
            // make controller
            $controllerContent = str_replace(
                ['{namespace}', '{className}', '{name}'],
                [$this->namespace, $this->className, $this->extensionName],
                file_get_contents(__DIR__.'/stubs/extension/controller.stub')
            );
            $this->putFile("src/Http/Controllers/{$this->className}Controller.php", $controllerContent);

            $viewContents = str_replace(
                ['{name}'],
                [$this->extensionName],
                file_get_contents(__DIR__.'/stubs/extension/view.stub')
            );
            $this->putFile('resources/views/index.blade.php', $viewContents);

            // make routes
            $routesContent = str_replace(
                ['{namespace}', '{className}', '{path}'],
                [$this->namespace, $this->className, $basePackage],
                file_get_contents(__DIR__.'/stubs/extension/routes.stub')
            );
            $this->putFile('src/Http/routes.php', $routesContent);
        }
    }

    protected function makeProviderContent()
    {
        if (! $this->option('theme')) {
            return <<<'TEXT'
protected $js = [
        'js/index.js',
    ];
TEXT;
        }

        return <<<'TEXT'
protected $type = self::TYPE_THEME;

TEXT;
    }

    protected function makeRegisterThemeContent()
    {
        if (! $this->option('theme')) {
            return;
        }

        return <<<'TEXT'
Admin::baseCss($this->formatAssetFiles($this->css));
TEXT;
    }

    protected function copyFiles()
    {
        $files = [
            $view = __DIR__.'/stubs/extension/view.stub' => 'resources/views/index.blade.php',
            $js = __DIR__.'/stubs/extension/js.stub'     => 'resources/assets/js/index.js',
            __DIR__.'/stubs/extension/css.stub'          => 'resources/assets/css/index.css',
            __DIR__.'/stubs/extension/.gitignore.stub'   => '.gitignore',
            __DIR__.'/stubs/extension/README.md.stub'    => 'README.md',
            __DIR__.'/stubs/extension/version.stub'      => 'version.php',
        ];

        if ($this->option('theme')) {
            unset($files[$view], $files[$js]);
        }

        $this->copy($files);
    }

    /**
     * Get root namespace for this package.
     *
     * @return array|null|string
     */
    protected function getRootNameSpace()
    {
        [$vendor, $name] = explode('/', $this->package);

        $default = str_replace(['-'], '', Str::title($vendor).'\\'.Str::title($name));

        if (! $namespace = $this->option('namespace')) {
            $namespace = $this->ask('Root namespace', $default);
        }

        return $namespace === 'default' ? $default : $namespace;
    }

    /**
     * Get extension class name.
     *
     * @return string
     */
    protected function getClassName()
    {
        return ucfirst(Str::camel(basename($this->package)));
    }

    /**
     * Create package dirs.
     */
    protected function makeDirs()
    {
        $this->makeDir($this->option('theme') ? $this->themeDirs : $this->dirs);
    }

    /**
     * Extension path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function extensionPath($path = '')
    {
        $path = rtrim($path, '/');

        if (empty($path)) {
            return rtrim($this->basePath, '/');
        }

        return rtrim($this->basePath, '/').'/'.ltrim($path, '/');
    }

    /**
     * Put contents to file.
     *
     * @param string $to
     * @param string $content
     */
    protected function putFile($to, $content)
    {
        $to = $this->extensionPath($to);

        $this->filesystem->put($to, $content);
    }

    /**
     * Copy files to extension path.
     *
     * @param string|array $from
     * @param string|null  $to
     */
    protected function copy($from, $to = null)
    {
        if (is_array($from) && is_null($to)) {
            foreach ($from as $key => $value) {
                $this->copy($key, $value);
            }

            return;
        }

        if (! file_exists($from)) {
            return;
        }

        $to = $this->extensionPath($to);

        $this->filesystem->copy($from, $to);
    }

    /**
     * Make new directory.
     *
     * @param array|string $paths
     */
    protected function makeDir($paths = '')
    {
        foreach ((array) $paths as $path) {
            $path = $this->extensionPath($path);

            $this->filesystem->makeDirectory($path, 0755, true, true);
        }
    }
}
