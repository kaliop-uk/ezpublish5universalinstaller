<?php

namespace Kaliop\eZP5UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kaliop\eZP5UI\Common\VarnishHandler;

class PurgeVarnish extends Command
{
    protected function configure()
    {
        $this
            ->setName('varnish:purge')
            ->setDescription('Purges the varnish cache by sending a BAN request for all locations')
            ->addOption('key', null, InputOption::VALUE_REQUIRED, 'The yaml config key storing the address of the server(s)', 'ezpublish.system.default.http_cache.purge_servers')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'The config file storing the address of the server(s)', 'ezpublish/config/ezpublish_{ENV}.yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null == ($env = $this->getApplication()->getEnv())) {
            throw new \Exception('Can not purge varnish cache: unknown environment!');
        }

        $handler = new VarnishHandler($output);
        $handler->purge($env, $input->getOption('key'), $input->getOption('file'));
    }
}
