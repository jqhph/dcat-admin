<?php

namespace Tests;

use Laravel\Dusk\Chrome\ChromeProcess as BaseChromeProcess;

class ChromeProcess extends BaseChromeProcess
{
    public function __construct($driver = null)
    {
        parent::__construct($driver);

        if ($this->onWindows() && is_file($driver = __DIR__.'/resources/drivers/chromedriver-win.exe')) {
            $this->driver = realpath($driver);
        } elseif ($this->onMac() && is_file($driver = __DIR__.'/resources/drivers/chromedriver-mac')) {
            $this->driver = realpath($driver);
        }
    }
}
