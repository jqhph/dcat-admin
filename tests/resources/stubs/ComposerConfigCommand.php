<?php

namespace App;

use Illuminate\Console\Command;

class ComposerConfigCommand extends Command
{
    protected $signature = 'admin:composer-config';

    public function handle()
    {
        $composer = base_path('composer.json');

        /* @var \Illuminate\Filesystem\Filesystem $files */
        $files = app('files');

        $contents = json_decode($files->get($composer), true);

        $contents['repositories'] = [
            [
                'type' => 'path',
                'url'  => './dcat-admin',
            ],
        ];

        $files->put($composer, str_replace('\\/', '/', json_encode($contents)));
    }
}
