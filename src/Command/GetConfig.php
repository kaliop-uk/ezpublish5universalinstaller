<?php

namespace Kaliop\eZP5UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kaliop\eZP5UI\Common\ConfigHandler;

class GetConfig extends Command
{
    protected function configure()
    {
        $this
            ->setName('config:get')
            ->setDescription('Returns a configuration value')
            ->addOption('key', null, InputOption::VALUE_REQUIRED, 'The yaml config key desired')
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'The config file storing the key', 'ezpublish/config/ezpublish_{ENV}.yml')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null == ($env = $this->getApplication()->getEnv())) {
            throw new \Exception('Can not read config: unknown environment!');
        }

        $handler = new ConfigHandler($output);
        $value = $handler->get($env, $input->getOption('key'), $input->getOption('file'));
        if (is_string($value)) {
            $output->write($value);
        } else {
            // hahaha! (evil grin)
            $output->writeln(json_encode($value));
        }
    }
}
