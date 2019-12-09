<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Dcat\Admin\Extension;
use Dcat\Admin\Support\Helper;
use Illuminate\Foundation\Console\VendorPublishCommand;
use Illuminate\Support\Arr;

class ImportCommand extends VendorPublishCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:import {extension?} {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a dcat-admin extension';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $extension = $this->argument('extension');

        $extensions = Admin::extensions();

        if (empty($extension) || ! Arr::has($extensions, $extension)) {
            $extension = $this->choice('Please choose a extension to import', array_keys($extensions));
        }

        $className = Arr::get($extensions, $extension);

        if (! class_exists($className) || ! is_subclass_of($className, Extension::class) || ! $className::make()->getName()) {
            $this->error("Invalid Extension [$className]");

            return;
        }
        /* @var Extension $extension */
        $extension = $className::make();

        $this->setServiceProvider($extension);

        $extension->import($this);

        $extensionName = $extension->getName();

        if ($assets = $extension->assets()) {
            $this->publishItem($assets, public_path('vendor/dcat-admin-extensions/'.$extensionName));
        }

        $this->publishTag(null);
        $this->call('view:clear');
        $this->call('admin:ide-helper');

        $this->updateExtensionConfig($className);

        $this->info("Extension [$className] imported");
    }

    protected function setServiceProvider(Extension $extension)
    {
        $this->provider = $extension->serviceProvider();
        $this->laravel->register($this->provider);
    }

    /**
     * @param $class
     *
     * @return bool
     */
    protected function updateExtensionConfig($class)
    {
        $config = (array) config('admin-extensions');

        $name = $class::NAME;

        $config[$name] = (array) ($config[$name] ?? []);

        $config[$name]['imported'] = true;
        $config[$name]['imported_at'] = date('Y-m-d H:i:s');

        return Helper::updateExtensionConfig($config);
    }
}
