<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:publish {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Re-publish dcat-admin's assets, configuration, language and migration files. If you want overwrite the existing files, you can add the `--force` option";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $force = $this->option('force');
        $options = ['--provider' => 'Dcat\Admin\AdminServiceProvider'];
        if ($force == true) {
            $options['--force'] = true;
        }
        $this->call('vendor:publish', $options);
        $this->call('view:clear');
    }
}
