<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class ExtensionRollbackCommand extends Command
{
    protected $signature = 'admin:extension-rollback 
     {name : The name of the extension. Eg: author-name/extension-name} 
     {--ver= : If this parameter is specified, the process will stop on the specified version, if not, it will completely rollback the extension. Example: 1.3.9} 
     {--force : Force rollback}';

    protected $description = 'Rollback an existing extension.';

    public function handle()
    {
    }
}
