<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the admin package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->call('admin:publish', ['--assets --migrations --force']);
            $this->call('migrate');
            $this->info('DcatAdmin has updated successfully!');
        } catch (\Exception $exception) {
            $this->error('Woops! Updating has failed: ' . $exception->getMessage());
        }

    }
}
