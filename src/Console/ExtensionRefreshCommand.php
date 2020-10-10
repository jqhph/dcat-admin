<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class ExtensionRefreshCommand extends Command
{
    protected $signature = 'admin:extension-refresh 
    {name : The name of the extension. Eg: author-name/extension-name} 
    {--path= : The path of the extension.}';

    protected $description = 'Removes and re-adds an existing extension.';

    public function handle()
    {
        $name = $this->argument('name');
        $path = $this->argument('path');
    }
}
