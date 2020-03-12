<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class AssetsLinkCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'admin:assets-link';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $publicPath = base_path('public/dcat-admin');

        if (file_exists(public_path($publicPath))) {
            return $this->error('The "public/dcat-admin" directory already exists.');
        }

        $distPath = realpath(__DIR__.'/../../resources/dist');

        $this->laravel->make('files')->link(
            $distPath, $publicPath
        );

        $this->info('The [public/dcat-admin] directory has been linked.');
    }
}
