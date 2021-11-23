<?php

namespace Kaliop\eZP5UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kaliop\eZP5UI\Common\MiscFilesHandler;

class InstallMiscFiles extends Command
{
    protected function configure()
    {
        $this
            ->setName('misc-files:install')
            ->setDescription('Deploys miscellaneous files via symlink or plain copy. Will place files in the same location from root dir.')
            ->addArgument('source-dir', InputArgument::OPTIONAL, 'The source directory', 'ezpublish/misc_files')
            ->addArgument('target-dir', InputArgument::OPTIONAL, 'The target directory for installation', getcwd())
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'If set, existing files colliding with symlinks will be removed before the symlink is made')
            ->addOption('relative', 'r', InputOption::VALUE_NONE, 'If set, the symlinks will use relative paths')
            ->addOption('backup', 'b', InputOption::VALUE_NONE, 'If set, existing files colliding with symlinks will be renamed before the symlink is made')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null == ($env = $this->getApplication()->getEnv())) {
            throw new \Exception('Can not install miscellaneous files: unknown environment!');
        }

        if ($input->getOption('backup') && !$input->getOption('overwrite')) {
            throw new \Exception("It does not make sense to use options 'backup' without 'overwrite'");
        }

        $handler = new MiscFilesHandler($input->getArgument('source-dir'), $input->getArgument('target-dir'), $output);
        $handler->install($env, $input->getOption('overwrite'), $input->getOption('relative'), $input->getOption('backup'));
    }
}
