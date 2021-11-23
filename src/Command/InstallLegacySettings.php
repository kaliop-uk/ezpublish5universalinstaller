<?php

namespace Kaliop\eZP5UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kaliop\eZP5UI\Common\LegacySettingsHandler;

class InstallLegacySettings extends Command
{
    protected function configure()
    {
        $this
            ->setName('legacy-settings:install')
            ->setDescription('Deploys legacy settings via symlink or plain copy')
            ->addArgument('source-dir', InputArgument::OPTIONAL, 'The source directory', 'ezpublish/legacy_settings')
            ->addArgument('target-dir', InputArgument::OPTIONAL, 'The target directory', 'ezpublish_legacy/settings')
            ->addOption('clean', null, InputOption::VALUE_NONE, 'If set, all existing legacy settings will be wiped before installing the new ones')
            ->addOption('relative', 'r', InputOption::VALUE_NONE, 'If set, the symlinks will use relative paths')
            ->addOption('backup', 'b', InputOption::VALUE_NONE, 'If set, existing files colliding with symlinks will be renamed before the symlink is made')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null == ($env = $this->getApplication()->getEnv())) {
            throw new \Exception('Can not install legacy settings: unknown environment!');
        }

        if ($input->getOption('clean') && $input->getOption('backup')) {
            throw new \Exception("It does not make sense to use options 'clean' and 'backup' together");
        }

        $handler = new LegacySettingsHandler($input->getArgument('source-dir'), $input->getArgument('target-dir'), $output);
        $handler->install($env, $input->getOption('clean'), $input->getOption('relative'), $input->getOption('backup'));
    }
}
