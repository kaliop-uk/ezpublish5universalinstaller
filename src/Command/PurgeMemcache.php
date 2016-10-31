<?php

namespace Kaliop\eZP5UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kaliop\eZP5UI\Common\MemcacheHandler;

class PurgeMemcache extends Command
{
    protected function configure()
    {
        $this
            ->setName('memcache:purge')
            ->setDescription('Purges Memcache sending a curl request')
            ->addOption('key', null, InputOption::VALUE_REQUIRED, 'The yaml config key storing the address of the server(s)', 'stash.caches.default.Memcache.servers')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'The config file storing the address of the server(s)', 'ezpublish/config/ezpublish_{ENV}.yml')
            ->addOption('server', null, InputOption::VALUE_REQUIRED, 'Pass in directly a hostname:port instead of looking it up in eZPublish configuration')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = new MemcacheHandler($output);

        if ($input->getOption('server') != null) {

            $def = explode(':', $input->getOption('server'));
            $handler->purgeServer($def[0], isset($def[1]) ? $def[1] : 11211);

        } else {

            if (null == ($env = $this->getApplication()->getEnv())) {
                throw new \Exception('Can not purge memcache: unknown environment!');
            }

            $handler->purge($env, $input->getOption('key'), $input->getOption('file'));
        }
    }
}
