<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    protected $signature = 'admin:update';

    protected $description = 'Update the admin package';

    public function handle()
    {
        $this->call('admin:publish', [
            '--assets'     => true,
            '--migrations' => true,
            '--lang'       => true,
            '--force'      => true,
        ]);
        $this->call('migrate');
    }
}
