<?php

namespace Kaliop\eZP5UI\Common;

use Symfony\Component\Console\Output\OutputInterface;

class Handler
{
    protected $outputInterface;

    protected function setOutputInterface(OutputInterface $outputInterface = null)
    {
        $this->outputInterface = $outputInterface;
    }

    protected function writeln($messages, $options = 0)
    {
        if ($this->outputInterface) {
            $this->outputInterface->writeln($messages, $options);
        }
    }
}
