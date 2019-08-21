<?php

namespace Tests\Feature;

use Tests\TestCase;

class InstallTest extends TestCase
{
    public function testInstalledDirectories()
    {
        $this->assertFileExists(admin_path());

        $this->assertFileExists(admin_path('Controllers'));

        $this->assertFileExists(admin_path('routes.php'));

        $this->assertFileExists(admin_path('bootstrap.php'));

        $this->assertFileExists(admin_path('Controllers/HomeController.php'));

        $this->assertFileExists(admin_path('Controllers/AuthController.php'));

        $this->assertFileExists(admin_path('Controllers/ExampleController.php'));

        $this->assertFileExists(config_path('admin.php'));

        $this->assertFileExists(public_path('vendor/dcat-admin'));

        $this->assertFileExists(database_path('migrations/2016_01_04_173148_create_admin_tables.php'));

        $this->assertFileExists(resource_path('lang/en/admin.php'));

        $this->assertFileExists(resource_path('lang/zh-CN/admin.php'));

        $this->assertFileExists(resource_path('lang/zh-CN/global.php'));
    }
}
