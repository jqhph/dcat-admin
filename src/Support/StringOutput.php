<?php

namespace Dcat\Admin\Support;

use Symfony\Component\Console\Output\Output;

class StringOutput extends Output
{
    public $output = '';

    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = false, $formatter = null)
    {
        $formatter = $formatter ?: new OutputFormatter();

        parent::__construct($verbosity, $decorated, $formatter);
    }

    public function clear()
    {
        $this->output = '';
    }

    protected function doWrite($message, $newline)
    {
        $this->output .= $message.($newline ? "\n" : '');
    }

    public function getContent()
    {
        return trim($this->output);
    }
}
