<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ExtensionInstallCommand extends Command
{
    protected $signature = 'admin:ext-install 
    {name : The name of the extension. Eg: author-name/extension-name} 
    {--path= : The path of the extension.}';

    protected $description = 'Install an extension';

    public function handle()
    {
        $name = $this->argument('name');
        $path = $this->option('path');

        $manager = Admin::extension()->setOutput($this->output);

        if ($path) {
            if (! is_file($path)) {
                $path = rtrim($path, '/').sprintf('/%s.zip', str_replace('/', '.', $name));
            }
        } else {
            $extensionDetails = $manager->requestDetails($name);

            $path = $hash = Arr::get($extensionDetails, 'hash');

            $this->output->writeln(sprintf('<info>Downloading extension: %s[%s]</info>', $name, $hash));

            $manager->download($name, $hash, true);
        }

        $this->output->writeln(sprintf('<info>Unpacking extension: %s</info>', $name));

        $manager->extract($path);

        $this->output->writeln(sprintf('<info>Migrating extension...</info>', $name));

        Admin::extension()->load();

        $manager
            ->updateManager()
            ->setOutPut($this->output)
            ->update($name);
    }
}
