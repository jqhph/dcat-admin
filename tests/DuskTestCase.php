<?php

namespace Tests;

use Dcat\Admin\Models\Administrator;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication, BrowserExtension, InteractsWithDatabase;

    /**
     * @var Administrator
     */
    protected $user;

    /**
     * @var bool
     */
    protected $login = true;

    public function login(Browser $browser)
    {
        $browser->loginAs($this->getUser(), 'admin');
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->extendBrowser();

        $this->boot();
    }

    public function tearDown(): void
    {
        $this->destory();

        parent::tearDown();
    }

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * @param  \Facebook\WebDriver\Remote\RemoteWebDriver  $driver
     * @return \Laravel\Dusk\Browser
     */
    protected function newBrowser($driver)
    {
        $browser = (new Browser($driver))->resize(1566, 1080);

        $browser->resolver->prefix = 'html';

        if ($this->login) {
            $this->login($browser);
        }

        return $browser;
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY_W3C, $options
            )
        );
    }

    /**
     * Build the process to run the Chromedriver.
     *
     * @param  array  $arguments
     * @return \Symfony\Component\Process\Process
     *
     * @throws \RuntimeException
     */
    protected static function buildChromeProcess(array $arguments = [])
    {
        return (new ChromeProcess(static::$chromeDriver))->toProcess($arguments);
    }
}
