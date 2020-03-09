<?php

namespace Dcat\Admin\Tests;

use Dcat\Admin\Models\Administrator;
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

        $this->boot();
    }


    public function tearDown(): void
    {
        $this->destory();

        parent::tearDown();
    }
}
