<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class MenuCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:menu-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush the menu cache';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $menuModel = config('admin.database.menu_model');
        $menuModel = new $menuModel();

        $menuModel->flushCache();

        $this->info('Menu cache cleared!');

        $menuModel->allNodes();

        $this->info('Menu cached successfully!');
    }
}
