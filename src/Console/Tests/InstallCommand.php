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

        $this->files->copyDirectory(realpath(__DIR__.'/../../../browser-tests'), base_path('tests'));
    }

    protected function initTestsDirectory()
    {
        $this->directory = base_path();
    }
}
