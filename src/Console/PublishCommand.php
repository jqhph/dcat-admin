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
    protected $signature = 'admin:publish 
    {--force : Overwrite any existing files} 
    {--lang : Publish language files} 
    {--assets : Publish assets files} 
    {--migrations : Publish migrations files} 
    {--config : Publish configuration files}';

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
        $options = ['--provider' => 'Dcat\Admin\AdminServiceProvider'];

        if ($this->option('force')) {
            $options['--force'] = true;
        }

        if ($this->option('lang')) {
            $options['--tag'] = 'dcat-admin-lang';
        } elseif ($this->option('migrations')) {
            $options['--tag'] = 'dcat-admin-migrations';
        } elseif ($this->option('assets')) {
            $options['--tag'] = 'dcat-admin-assets';
        } elseif ($this->option('config')) {
            $options['--tag'] = 'dcat-admin-config';
        }

        $this->call('vendor:publish', $options);
        $this->call('view:clear');
    }
}
