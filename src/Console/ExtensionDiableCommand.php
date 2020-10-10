<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class ExtensionDiableCommand extends Command
{
    protected $signature = 'admin:extension-disable {name : The name of the extension. Eg: author-name/extension-name} ';

    protected $description = 'Disable an existing extension.';

    public function handle()
    {
        $name = $this->argument('name');
    }
}
