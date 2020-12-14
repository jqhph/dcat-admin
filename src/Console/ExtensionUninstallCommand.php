<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Illuminate\Console\Command;

class ExtensionUninstallCommand extends Command
{
    protected $signature = 'admin:ext-uninstall 
    {name : The name of the extension. Eg: author-name/extension-name}';

    protected $description = 'Uninstall an existing extension';

    public function handle()
    {
        $name = $this->argument('name');

        $confirmQuestion = 'Please confirm that you wish to completely rollback this extension. This may result in potential data loss.';

        if ($this->confirm($confirmQuestion)) {
            try {
                Admin::extension()
                    ->updateManager()
                    ->setOutPut($this->output)
                    ->uninstall($name);
            } catch (\Throwable $exception) {
                $lastVersion = Admin::extension()->versionManager()->getCurrentVersion($name);

                $this->output->writeln(sprintf('<comment>An exception occurred during the rollback and the process has been stopped. The extension was rolled back to version v%s.</comment>', $lastVersion));

                throw $exception;
            }
        }
    }
}
