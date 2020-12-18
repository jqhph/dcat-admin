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

        $tags = $this->getTags();

        foreach ($tags as $tag) {
            $this->call('vendor:publish', $options + ['--tag' => $tag]);
        }

        if (! $tags) {
            $this->call('vendor:publish', $options);
        }

        $this->call('view:clear');
    }

    protected function getTags()
    {
        $tags = [];

        if ($this->option('lang')) {
            $tags[] = 'dcat-admin-lang';
        }
        if ($this->option('migrations')) {
            $tags[] = 'dcat-admin-migrations';
        }
        if ($this->option('assets')) {
            $tags[] = 'dcat-admin-assets';
        }
        if ($this->option('config')) {
            $tags[] = 'dcat-admin-config';
        }

        return $tags;
    }
}
