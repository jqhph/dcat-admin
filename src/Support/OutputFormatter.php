<?php

namespace Dcat\Admin\Support;

class OutputFormatter extends \Symfony\Component\Console\Formatter\OutputFormatter
{
    public function format($message)
    {
        return $message;
    }
}
