<?php

namespace Tests;

use Laravel\Dusk\Chrome\ChromeProcess as BaseChromeProcess;

class ChromeProcess extends BaseChromeProcess
{
    public function __construct($driver = null)
    {
        parent::__construct($driver);

        if ($this->onWindows()) {
            $this->driver = realpath(__DIR__.'/resources/drivers/chromedriver-win.exe');
        } elseif ($this->onMac()) {
            $this->driver = realpath(__DIR__.'/resources/drivers/chromedriver-mac');
        } else {
            $this->driver = realpath(__DIR__.'/resources/drivers/chromedriver-linux');
        }
    }
}
