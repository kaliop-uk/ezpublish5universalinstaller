<?php

namespace Kaliop\eZP5UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kaliop\eZP5UI\Common\LegacySettingsHandler;

class PurgeLegacySettings extends Command
{
    protected function configure()
    {
        $this
            ->setName('legacy-settings:purge')
            ->setDescription('Removes all legacy settings in override and siteaccess dirs')
            ->addArgument('target-dir', InputArgument::OPTIONAL, 'The target directory', 'ezpublish_legacy/settings')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = new LegacySettingsHandler('', $input->getArgument('target-dir'));
        $handler->cleanUpTarget();
    }
}
