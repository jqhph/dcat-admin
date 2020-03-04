<?php

namespace Dcat\Admin\Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app->register('Dcat\Admin\AdminServiceProvider');

        $app->make('config')->set('app.locale', 'en');

        return $app;
    }
}
