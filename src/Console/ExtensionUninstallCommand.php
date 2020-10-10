<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class ExtensionUninstallCommand extends Command
{
    protected $signature = 'admin:extension-uninstall 
    {name : The name of the extension. Eg: author-name/extension-name}';

    protected $description = 'Uninstall an existing extension.';

    public function handle()
    {
    }
}
