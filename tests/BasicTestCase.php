<?php

namespace Tests;

use Dcat\Admin\Models\Administrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait BasicTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $adminConfig = require __DIR__.'/config/admin.php';

        $this->app['config']->set('database.default', 'mysql');
        $this->app['config']->set('database.connections.mysql.host', env('MYSQL_HOST', 'localhost'));
        $this->app['config']->set('database.connections.mysql.database', 'laravel_dcat_admin_test');
        $this->app['config']->set('database.connections.mysql.username', 'root');
        $this->app['config']->set('database.connections.mysql.password', '');
        $this->app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');
        $this->app['config']->set('filesystems', require __DIR__.'/config/filesystems.php');
        $this->app['config']->set('admin', $adminConfig);

        foreach (Arr::dot(Arr::get($adminConfig, 'auth'), 'auth.') as $key => $value) {
            $this->app['config']->set($key, $value);
        }

        $this->artisan('vendor:publish', ['--provider' => 'Dcat\Admin\AdminServiceProvider']);

        Schema::defaultStringLength(191);

        $this->artisan('admin:install');

        $this->migrateTestTables();

        if (file_exists($routes = admin_path('routes.php'))) {
            require $routes;
        }

        require __DIR__.'/routes.php';

        require __DIR__.'/seeds/factory.php';

        view()->addNamespace('admin-tests', __DIR__.'/views');

        if ($this->login) {
            $this->be($this->getUser(), 'admin');
        }
    }

    /**
     * @return Administrator
     */
    protected function getUser()
    {
        if ($this->user) {
            return $this->user;
        }

        return $this->user = Administrator::first();
    }

    public function tearDown(): void
    {
        (new \CreateAdminTables())->down();

        (new \CreateTestTables())->down();

        DB::select("delete from `migrations` where `migration` = '2016_01_04_173148_create_admin_tables'");
        DB::select("delete from `migrations` where `migration` = '2016_11_22_093148_create_test_tables'");

        parent::tearDown();
    }

    /**
     * run package database migrations.
     *
     * @return void
     */
    public function migrateTestTables()
    {
        $fileSystem = new Filesystem();

        $fileSystem->requireOnce(__DIR__.'/migrations/2016_11_22_093148_create_test_tables.php');

        (new \CreateTestTables())->up();
    }
}
