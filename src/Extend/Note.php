<?php

namespace Dcat\Admin\Extend;

use Symfony\Component\Console\Output\OutputInterface;

trait Note
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    public $output;

    /**
     * @var array
     */
    public $notes = [];

    public function note($message)
    {
        if ($this->output instanceof OutputInterface) {
            $this->output->writeln($message);
        } else {
            $this->notes[] = $message;
        }
    }

    public function setOutPut($output)
    {
        $this->output = $output;

        return $this;
    }
}
