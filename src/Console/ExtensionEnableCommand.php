<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class ExtensionEnableCommand extends Command
{
    protected $signature = 'admin:extension-enable 
    {name : The name of the extension. Eg: author-name/extension-name}';

    protected $description = 'Enable an existing extension.';

    public function handle()
    {
        $name = $this->argument('name');



    }
}
