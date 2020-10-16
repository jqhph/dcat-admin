<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Illuminate\Console\Command;

class ExtensionRollbackCommand extends Command
{
    protected $signature = 'admin:ext-rollback 
     {name : The name of the extension. Eg: author-name/extension-name} 
     {ver : If this parameter is specified, the process will stop on the specified version, if not, it will completely rollback the extension. Example: 1.3.9} 
     {--force : Force rollback}';

    protected $description = 'Rollback an existing extension';

    public function handle()
    {
        $name = $this->argument('name');

        if (! Admin::extension()->has($name)) {
            throw new \InvalidArgumentException('Extension not found');
        }

        $stopOnVersion = ltrim(($this->argument('ver') ?: null), 'v');

        if ($stopOnVersion) {
            if (! Admin::extension()->versionManager()->hasDatabaseVersion($name, $stopOnVersion)) {
                throw new \InvalidArgumentException('Extension version not found');
            }
            $confirmQuestion = 'Please confirm that you wish to revert the extension to version '.$stopOnVersion.'. This may result in changes to your database and potential data loss.';
        } else {
            $confirmQuestion = 'Please confirm that you wish to completely rollback this extension. This may result in potential data loss.';
        }

        if ($this->option('force') || $this->confirm($confirmQuestion)) {
            try {
                Admin::extension()
                    ->updateManager()
                    ->setOutPut($this->output)
                    ->rollback($name, $stopOnVersion);
            } catch (\Throwable $exception) {
                $lastVersion = Admin::extension()->versionManager()->getCurrentVersion($name);

                $this->output->writeln(sprintf('<comment>An exception occurred during the rollback and the process has been stopped. The extension was rolled back to version v%s.</comment>', $lastVersion));

                throw $exception;
            }
        }
    }
}
