<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Support\Helper;
use Illuminate\Console\GeneratorCommand as BaseCommand;
use Illuminate\Support\Str;

abstract class GeneratorCommand extends BaseCommand
{
    protected $baseDirectory;

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->getDefaultNamespace(null);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return Helper::guessClassFileName($name);
    }

    /**
     * @return string
     */
    protected function getBaseDir()
    {
        if ($this->baseDirectory) {
            return trim(base_path($this->baseDirectory), '/');
        }

        if ($this->hasOption('base') && $this->option('base')) {
            return trim(base_path($this->option('base')), '/');
        }

        return $this->laravel['path'];
    }

    /**
     * @return void
     */
    protected function askBaseDirectory()
    {
        if (! Str::startsWith(config('admin.route.namespace'), 'App')) {
            $dir = explode('\\', config('admin.route.namespace'))[0];

            $this->baseDirectory = trim($this->ask('Please enter the application path', Helper::slug($dir)));
        }
    }
}
