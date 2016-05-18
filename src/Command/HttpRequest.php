<?php

namespace Kaliop\eZP5UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kaliop\eZP5UI\Common\HttpRequestHandler;

class HttpRequest extends Command
{
    protected function configure()
    {
        $this
            ->setName('http:request')
            ->setDescription('Sends http requests based on values in config files')
            ->addOption('key', null, InputOption::VALUE_REQUIRED, 'The yaml config key storing the url(s) to be requested')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'The config file storing the configuration', 'ezpublish/config/parameters_{ENV}.yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null == ($env = $this->getApplication()->getEnv())) {
            throw new \Exception('Can not configure http requests: unknown environment!');
        }

        $handler = new HttpRequestHandler();
        $handler->execute($env, $input->getOption('key'), $input->getOption('file'));
    }
}
