<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Illuminate\Console\Command;

class ExtensionRefreshCommand extends Command
{
    protected $signature = 'admin:ext-refresh 
    {name : The name of the extension. Eg: author-name/extension-name} 
    {--path= : The path of the extension.}';

    protected $description = 'Removes and re-adds an existing extension';

    public function handle()
    {
        $name = $this->argument('name');

        if (! Admin::extension()->has($name)) {
            throw new \InvalidArgumentException(sprintf('Plugin "%s" not found.', $name));
        }

        $confirmQuestion = 'Please confirm that you wish to reinstall this extension?';

        if (! $this->confirm($confirmQuestion)) {
            return;
        }

        $manager = Admin::extension()
            ->updateManager()
            ->setOutPut($this->output);

        $manager->rollback($name);

        $this->output->writeln('<info>Reinstalling extension...</info>');

        $manager->update($name);
    }
}
