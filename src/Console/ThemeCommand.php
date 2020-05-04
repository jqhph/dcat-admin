<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use TitasGailius\Terminal\Terminal;

class ThemeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:theme {name} 
        {--color= : Theme color} 
        {--publish : Publish assets files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile theme files.';

    /**
     * @var array
     */
    protected $themes = [
        'indigo'     => '$indigo',
        'blue'       => '#5686d4',
        'blue-light' => '#4199de',
        'green'      => '#4e9876',
    ];

    /**
     * @var string
     */
    protected $packagePath;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! class_exists(Terminal::class)) {
            throw new \RuntimeException('Please install "titasgailius/terminal" first!');
        }

        $this->packagePath = realpath(__DIR__.'/../..');
        $this->files = $this->laravel['files'];

        $name = $this->argument('name');
        $publish = $this->option('publish');
        $color = $this->getColor($name);

        $this->backupFiles();
        $this->replaceFiles($name, $color);

        try {
            $this->installNodeModules();

            $this->info('npm run production...');

            // 编译
            $response = Terminal::builder()
                ->timeout(900)
                ->run("cd {$this->packagePath} && npm run prod");

            $this->info($response->output());

            // 重置文件
            $this->resetFiles();

            if ($publish) {
                $this->publishAssets();
            }

        } catch (\Throwable $e) {
            $this->resetFiles();

            throw $e;
        }
    }

    /**
     * 发布静态资源.
     */
    protected function publishAssets()
    {
        $options = ['--provider' => 'Dcat\Admin\AdminServiceProvider', '--force' => true, '--tag' => 'dcat-admin-assets'];

        $this->call('vendor:publish', $options);
    }

    /**
     * 替换文件.
     *
     * @param $name
     * @param $color
     */
    protected function replaceFiles($name, $color)
    {
        $mixFile = $this->getMixFile();
        $contents = str_replace('let theme = null', "let theme = '{$name}'", $this->files->get($mixFile));
        $this->files->put($mixFile, $contents);

        $colorFile = $this->getColorFile();
        $contents = str_replace('$primary: $indigo;', "\$primary: $color;", $this->files->get($colorFile));
        $this->files->put($colorFile, $contents);
    }

    /**
     * 备份文件.
     */
    protected function backupFiles()
    {
        $this->files->delete($this->getMixBakFile());
        $this->files->copy($this->getMixFile(), $this->getMixBakFile());

        $this->files->delete($this->getColorBakFile());
        $this->files->copy($this->getColorFile(), $this->getColorBakFile());
    }

    /**
     * 重置文件.
     */
    protected function resetFiles()
    {
        $mixFile = $this->getMixFile();
        $mixBakFile = $this->getMixBakFile();

        $this->files->delete($mixFile);
        $this->files->copy($mixBakFile, $mixFile);
        $this->files->delete($mixBakFile);

        $colorFile = $this->getColorFile();
        $colorBakFile = $this->getColorBakFile();

        $this->files->delete($colorFile);
        $this->files->copy($colorBakFile, $colorFile);
        $this->files->delete($colorBakFile);
    }

    /**
     * @return string
     */
    protected function getMixFile()
    {
        return $this->packagePath.'/webpack.mix.js';
    }

    /**
     * @return mixed
     */
    protected function getMixBakFile()
    {
        return str_replace('.js', '.bak.js', $this->getMixFile());
    }

    /**
     * @return string
     */
    protected function getColorFile()
    {
        return $this->packagePath.'/resources/assets/dcat/sass/theme/_colors.scss';
    }

    /**
     * @return mixed
     */
    protected function getColorBakFile()
    {
        return str_replace('.scss', '.bak.scss', $this->getColorFile());
    }

    /**
     * 安装node依赖.
     */
    protected function installNodeModules()
    {
        if (is_dir($this->packagePath.'/node_modules')) {
            return;
        }

        $this->info('npm install...');

        $response = Terminal::builder()
            ->timeout(1800)
            ->run("npm install");

        $this->line($response);
    }

    /**
     * 获取颜色.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getColor($name)
    {
        INPUT_COLOR:

        $color = $this->option('color');

        if (! $color && isset($this->themes[$name])) {
            return $this->themes[$name];
        }

        if (! $color) {
            $color = $this->formatColor($this->ask('Please enter a color code(hex)'));
        }

        if (! $color) {
            goto INPUT_COLOR;
        }

        return $this->formatColor($color);
    }

    /**
     * @param string $color
     *
     * @return string
     */
    protected function formatColor($color)
    {
        if ($color && ! Str::startsWith($color, '#')) {
            $color = "#$color";
        }

        return $color;
    }
}
