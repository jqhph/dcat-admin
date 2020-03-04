<?php

namespace Dcat\Admin\Tests;

use Dcat\Admin\Models\Administrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUrl = 'http://localhost:8000';

    /**
     * @var Administrator
     */
    protected $user;

    protected $login = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->config();

        $this->artisan('admin:publish');

        Schema::defaultStringLength(191);

        $this->artisan('admin:install');

        $this->migrateTestTables();

        if (file_exists($routes = admin_path('routes.php'))) {
            require $routes;
        }

        require __DIR__.'/routes.php';

        require __DIR__.'/resources/seeds/factory.php';

        view()->addNamespace('admin-tests', __DIR__.'/resources/views');

        if ($this->login) {
            $this->be($this->getUser(), 'admin');
        }
    }

    protected function config()
    {
        $adminConfig = require __DIR__.'/resources/config/admin.php';

        $config = $this->app['config'];

        $config->set('database.default', 'mysql');
        $config->set('database.connections.mysql.host', env('MYSQL_HOST', 'localhost'));
        $config->set('database.connections.mysql.database', 'laravel_dcat_admin_test');
        $config->set('database.connections.mysql.username', env('MYSQL_USER', 'root'));
        $config->set('database.connections.mysql.password', env('MYSQL_PASSWORD', ''));
        $config->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');
        $config->set('filesystems', require __DIR__.'/resources/config/filesystems.php');
        $config->set('admin', $adminConfig);
        $config->set('app.debug', true);

        foreach (Arr::dot(Arr::get($adminConfig, 'auth'), 'auth.') as $key => $value) {
            $this->app['config']->set($key, $value);
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

        $fileSystem->requireOnce(__DIR__.'/resources/migrations/2016_11_22_093148_create_test_tables.php');

        (new \CreateTestTables())->up();
    }
}
