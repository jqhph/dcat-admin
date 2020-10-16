<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Illuminate\Console\Command;

class ExtensionEnableCommand extends Command
{
    protected $signature = 'admin:ext-enable 
    {name : The name of the extension. Eg: author-name/extension-name}';

    protected $description = 'Enable an existing extension';

    public function handle()
    {
        $extensionManager = Admin::extension();

        $name = $this->argument('name');

        if (! $extensionManager->has($name)) {
            return $this->error(sprintf('Unable to find a registered extension called "%s"', $name));
        }

        $extensionManager->enable($name);

        $this->output->writeln(sprintf('<info>%s:</info> enabled.', $name));
    }
}
