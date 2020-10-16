<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Illuminate\Console\Command;

class ExtensionDiableCommand extends Command
{
    protected $signature = 'admin:ext-disable {name : The name of the extension. Eg: author-name/extension-name} ';

    protected $description = 'Disable an existing extension';

    public function handle()
    {
        $extensionManager = Admin::extension();

        $name = $this->argument('name');

        if (! $extensionManager->has($name)) {
            return $this->error(sprintf('Unable to find a registered extension called "%s"', $name));
        }

        $extensionManager->enable($name, false);

        $this->output->writeln(sprintf('<info>%s:</info> disabled.', $name));
    }
}
