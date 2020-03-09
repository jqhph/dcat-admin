<?php

namespace Dcat\Admin\Console\Tests;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    protected $signature = 'admin:tests:install';

    protected $description = 'Install the admin tests package';

    /**
     * @var Filesystem
     */
    protected $files;

    protected $directory;

    public function handle()
    {
        $this->files = app('files');

        $this->initTestsDirectory();

        $this->files->link(realpath(__DIR__.'/../../../tests'), $this->directory);

        $this->info('The [tests/] directory has been linked.');
    }

    protected function initTestsDirectory()
    {
        $dir = realpath(__DIR__.'/../../../vendor/laravel/laravel/tests');
        if ($this->files->isDirectory($dir)) {
            $this->directory = $dir;

            $this->files->deleteDirectory($this->directory);
            $this->files->makeDirectory($this->directory);

            return;
        }

        $this->directory = base_path();
    }
}
