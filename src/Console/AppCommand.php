<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Support\Helper;
use Illuminate\Filesystem\Filesystem;

class AppCommand extends InstallCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:app {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->addConfig();
        $this->initAdminDirectory();

        $this->info('Done.');
    }

    protected function addConfig()
    {
        /* @var Filesystem $files */
        $files = $this->laravel['files'];

        $app = Helper::slug($namespace = $this->argument('name'));

        $files->put(
            $config = config_path($app.'.php'),
            str_replace(
                ['DummyNamespace', 'DummyApp'],
                [$namespace, $app],
                $files->get(__DIR__.'/stubs/config.stub')
            )
        );

        config(['admin' => include $config]);
    }

    /**
     * Set admin directory.
     *
     * @return void
     */
    protected function setDirectory()
    {
        $this->directory = app_path($this->argument('name'));
    }
}
