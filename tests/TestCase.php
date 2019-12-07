<?php

namespace Tests;

use Dcat\Admin\Models\Administrator;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use CreatesApplication,
        BasicTestCase;

    protected $baseUrl = 'http://localhost:8000';

    /**
     * @var Administrator
     */
    protected $user;

    protected $login = true;
}
