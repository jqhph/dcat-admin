<?php

namespace Tests;

use Dcat\Admin\Models\Administrator;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require $this->getAppPath();

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function boot()
    {
        $this->artisan('admin:publish');

        Schema::defaultStringLength(191);

        $this->artisan('admin:install');

        $this->migrateTestTables();

        require __DIR__.'/helpers.php';

        require __DIR__.'/resources/seeds/factory.php';

        view()->addNamespace('admin-tests', __DIR__.'/resources/views');
    }

    protected function getAppPath()
    {
        $path = __DIR__.'/../bootstrap/app.php';

        if (! is_file($path)) {
            $path = __DIR__.'/../../bootstrap/app.php';
        }
        if (! is_file($path)) {
            $path = __DIR__.'/../../../bootstrap/app.php';
        }

        return $path;
    }

    protected function destory()
    {
        //(new \CreateAdminTables())->down();
        //(new \CreateAdminSettingsTable())->down();
        //(new \CreateAdminExtensionsTable())->down();
        //(new \UpdateAdminMenuTable())->down();
        //
        (new \CreateTestTables())->down();

        //DB::select("delete from `migrations` where `migration` = '2016_01_04_173148_create_admin_tables'");
        //DB::select("delete from `migrations` where `migration` = '2020_09_07_090635_create_admin_settings_table'");
        //DB::select("delete from `migrations` where `migration` = '2020_09_22_015815_create_admin_extensions_table'");
        //DB::select("delete from `migrations` where `migration` = '2020_11_01_083237_update_admin_menu_table'");
        DB::select("delete from `migrations` where `migration` = '2016_11_22_093148_create_test_tables'");

        Artisan::call('migrate:rollback');
    }

    public function migrateTestTables()
    {
        $fileSystem = new Filesystem();

        $fileSystem->requireOnce(__DIR__.'/resources/migrations/2016_11_22_093148_create_test_tables.php');

        (new \CreateTestTables())->up();
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
}
